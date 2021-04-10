<?php namespace App\Currency\Native;

use App\Currency\Currency;
use App\Currency\Option\WalletOption;
use App\User;
use Bezhanov\Ethereum\Converter;
use Ethereum\Abi;
use Illuminate\Support\Facades\Log;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Web3;

class Ethereum extends Currency {

    function id(): string {
        return "native_eth";
    }

    public function walletId(): string {
        return "eth";
    }

    function name(): string {
        return "ETH";
    }

    public function alias(): string {
        return "ethereum";
    }

    public function displayName(): string {
        return "Ethereum";
    }

    function icon(): string {
        return "eth";
    }

    public function style(): string {
        return "#627eea";
    }

    public function isRunning(): bool {
        return true;
    }

    public function newWalletAddress(): string {
        $returnedValue = 'Error';

        $web3 = $this->getClient();
        $web3->getPersonal()->newAccount(auth('sanctum')->user()->_id, function($err, $account) use(&$returnedValue) {
            if($err !== null) {
                Log::critical($err);
                return null;
            }

            $returnedValue = $account;
        });
        return $returnedValue;
    }

    private function balance($account) {
        return -1;
    }

    public function setupWallet() {}

    public function send(string $from, string $to, float $sum) {
        $password = User::where('wallet_native_eth', $from)->first()->_id;

        $this->getClient()->getPersonal()->unlockAccount($from, $password, function ($err, $unlocked) use($to, $sum, $from) {
            if($err != null) {
                Log::critical($err);
                return;
            }

            $this->getClient()->getEth()->sendTransaction([
                'to' => $to,
                'from' => $from,
                'value' => '0x' . (new Converter())->toWei(strval($sum), 'ether')
            ], function ($err) {
                if ($err !== null) Log::critical($err);
            });
        });
    }

    public function hotWalletBalance(): float {
        return $this->balance($this->option('withdraw_address')) ?? -1;
    }

    public function coldWalletBalance(): float {
        return $this->balance($this->option('transfer_address')) ?? -1;
    }

    protected function getClient() {
        return new Web3(new HttpProvider(new HttpRequestManager('http://localhost:8545', 30)));
    }

    public function process(string $wallet = null) {
        $this->getClient()->getEth()->getTransactionByHash($wallet, function($err, $response) use($wallet) {
            if($err != null) {
                Log::critical($err);
                return;
            }

            if($response == null) {
                Log::error('Invalid native_eth transaction response (null) for ' . $wallet);
                return;
            }

            //if(isset($response->blockNumber)) $confirmations = intval($number->toString()) - hexdec($response->blockNumber);
            if(isset($response->to) && isset($response->blockNumber)) {
                $confirmations = intval(Currency::find('native_eth')->option('confirmations'));

                if(!$this->accept($confirmations, $response->to, $wallet, hexdec($response->value) / pow(10, 18))) {
                    $this->getClient()->getEth()->getLogs([
                        'address' => $response->to,
                        'fromBlock' => $response->blockNumber,
                        'toBlock' => $response->blockNumber
                    ], function($err, $response) use ($wallet, $confirmations) {
                        if($err != null) {
                            Log::critical('Invalid ETH token transaction ' . $wallet);
                            return;
                        }

                        foreach ($response as $log) {
                            $contract = Abi::decode([
                                (object) ['type' => 'address', 'name' => 'from'],
                                (object) ['type' => 'address', 'name' => 'ignore1'],
                                (object) ['type' => 'bytes24', 'name' => 'ignore2'],
                                (object) ['type' => 'address', 'name' => 'to'],
                                (object) ['type' => 'uint256', 'name' => 'amount'],
                                (object) ['type' => 'uint256', 'name' => 'ignore3'],
                                (object) ['type' => 'uint256', 'name' => 'ignore4']
                            ], str_replace('0x', '', $log->data));

                            $this->accept($confirmations, '0x'.$contract[3]->val(), $wallet, floatval($contract[4]->val()) / pow(10, 18));
                        }
                    });
                }
            }
        });
    }

    protected function options(): array {
        return [];
    }

}
