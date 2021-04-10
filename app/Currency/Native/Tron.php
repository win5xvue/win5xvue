<?php namespace App\Currency\Native;

use App\Currency\Currency;
use App\Currency\Option\WalletOption;
use App\User;
use IEXBase\TronAPI\Provider\HttpProvider;

class Tron extends Currency {

    function id(): string {
        return 'native_trx';
    }

    public function walletId(): string {
        return "trx";
    }

    function name(): string {
        return 'TRX';
    }

    public function alias(): string {
        return 'tron';
    }

    public function displayName(): string {
        return "Tron (TRX)";
    }

    function icon(): string {
        return 'trx';
    }

    public function style(): string {
        return "#eb0a29";
    }

    public function isRunning(): bool {
        try {
            $this->coldWalletBalance();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    function newWalletAddress(): string {
        try {
            $tron = $this->client();
            $account = $tron->createAccount();
            auth('sanctum')->user()->update([
                'wallet_trx_private_key' => $account['privateKey']
            ]);
            return $account['address'];
        } catch (\Exception $e) {
            return 'Error';
        }
    }

    public function process(string $wallet = null) {
        $client = $this->client();
        $payments = $client->getManager()->request(sprintf('v1/accounts/%s/transactions', $wallet), [
            'only_confirmed' => 'true',
            'only_to' => 'true',
            'limit' => 200
        ], 'get')['data'];
        foreach($payments as $payment) $this->accept(1, $wallet, $payment['txID'], $client->fromTron($payment['raw_data']['contract'][0]['parameter']['value']['amount']));
    }

    public function setupWallet() {
        try {
            $tron = $this->client();
            $depositAccount = $tron->createAccount();
            $withdrawAccount = $tron->createAccount();
        } catch (\Exception $e) {
            return null;
        }

        $this->option('transfer_address', $depositAccount['address']);
        $this->option('withdraw_address', $withdrawAccount['address']);
        $this->option('trx_private_key', $depositAccount['privateKey']);
        $this->option('trx_withdraw_private_key', $withdrawAccount['privateKey']);
    }

    public function send(string $from, string $to, float $sum) {
        $client = $this->client();
        $user = User::where('wallet_trx', $from)->first();

        $client->setPrivateKey($user == null ? $this->option('trx_private_key') : $user->wallet_trx_private_key);
        $client->sendRawTransaction($client->signTransaction($client->getTransactionBuilder()->sendTrx($to, $sum, $from)));
    }

    public function coldWalletBalance(): float {
        $client = $this->client();
        $client->setAddress($this->option('transfer_address'));
        return $client->getBalance($this->option('transfer_address'), true);
    }

    public function hotWalletBalance(): float {
        $client = $this->client();
        $client->setAddress($this->option('withdraw_address'));
        return $client->getBalance($this->option('withdraw_address'), true);
    }

    /** @throws \Exception */
    private function client() {
        $api = env('APP_DEBUG') ? 'https://api.shasta.trongrid.io' : 'https://api.trongrid.io';
        $fullNode = new HttpProvider($api);
        $solidityNode = new HttpProvider($api);
        $eventServer = new HttpProvider($api);

        return new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
    }

    protected function options(): array {
        return [
            new class extends WalletOption {
                public function id() {
                    return 'trx_private_key';
                }

                public function name(): string {
                    return 'TRX deposit wallet private key';
                }

                public function readOnly(): bool {
                    return true;
                }
            },
            new class extends WalletOption {
                public function id() {
                    return 'trx_withdraw_private_key';
                }

                public function name(): string {
                    return 'TRX withdraw wallet private key';
                }

                public function readOnly(): bool {
                    return true;
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
