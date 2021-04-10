<?php namespace App\Currency\Native;

use App\Currency\Currency;
use App\Currency\Option\WalletOption;
use Illuminate\Support\Facades\Log;
use Nbobtc\Command\Command;
use Nbobtc\Http\Client;

/**
 * bitcoind forks
 * @package App\Currency
 */
abstract class BitcoinCurrency extends Currency {

    public function process(string $wallet = null) {
        $client = $this->getClient();
        $command = new Command('gettransaction', $wallet);
        $response = $client->sendCommand($command);
        $contents = json_decode($response->getBody()->getContents(), true)['result'];

        if(isset($contents['details'][0]['address'])) $this->accept($contents['confirmations'], $contents['details'][0]['address'], $contents['hex'], abs($contents['details'][0]['amount']));
        else if(isset($contents['details']['address'])) $this->accept($contents['confirmations'], $contents['details']['address'], $contents['hex'], abs($contents['details']['amount']));
    }

    public function processBlock($blockId) {
        $client = $this->getClient();
        $response = json_decode($client->sendCommand(new Command('listtransactions'))->getBody()->getContents(), true)['result'];
        foreach($response as $tx) $this->process($tx['txid']);
    }

    function newWalletAddress($accountName = null): string {
        try {
            $client = $this->getClient();
            $command = new Command('getnewaddress', $accountName == null ? auth('sanctum')->user()->_id : $accountName);
            $response = $client->sendCommand($command);
            $contents = json_decode($response->getBody()->getContents());
            if($contents->error != null) throw new \Exception('Exception during getnewaddress');
            return $contents->result;
        } catch (\Exception $e) {
            Log::critical($e);
            return 'Error';
        }
    }

    public function setupWallet(): ?string {
        try {
            $depositAccount = $this->newWalletAddress('deposit');
            $withdrawAccount = $this->newWalletAddress('withdraw');

            if($depositAccount === 'Error' || $withdrawAccount === 'Error') return null;

            $this->option('transfer_address', $depositAccount);
            $this->option('withdraw_address', $withdrawAccount);

            $this->getClient()->sendCommand(new Command('backupwallet', storage_path('app/'.$this->id().'_wallet.dat')));
        } catch (\Exception $e) {
            return null;
        }

        return $this->id().'_wallet.dat';
    }

    public function getClient(): Client {
        return new Client($this->option('rpc'));
    }

    protected function options(): array {
        return [
            new class extends WalletOption {
                function id() {
                    return "rpc";
                }

                function name(): string {
                    return "RPC URL";
                }
            },
            new class extends WalletOption {
                public function id() {
                    return "transfer_address";
                }

                public function name(): string {
                    return "Transfer deposits to this address";
                }

                public function readOnly(): bool {
                    return true;
                }
            },
            new class extends WalletOption {
                public function id() {
                    return 'withdraw_address';
                }

                public function name(): string {
                    return 'Transfer withdraws from this address';
                }

                public function readOnly(): bool {
                    return true;
                }
            }
        ];
    }

}
