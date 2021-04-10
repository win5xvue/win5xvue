<?php namespace App\Currency\BitGo;

use App\Currency\Currency;
use App\Currency\Option\WalletOption;
use Illuminate\Support\Facades\Log;
use neto737\BitGoSDK\BitGoSDK;
use neto737\BitGoSDK\Enum\AddressType;
use ReflectionClass;

abstract class BitGoCurrency extends Currency {

    private array $allowedCoins = ['btc', 'bch', 'bsv', 'btg', 'eth', 'dash', 'ltc', 'xrp', 'zec', 'rmg', 'erc', 'omg', 'zrx', 'fun', 'gnt', 'rep', 'bat', 'knc', 'cvc', 'eos', 'qrl', 'nmr', 'pay', 'brd', 'trx', 'tron', 'tbtc', 'tbch', 'tbsv', 'teth', 'tdash', 'tltc', 'txrp', 'tzec', 'trmg', 'terc'];

    abstract function getCurrencyCode();

    function newWalletAddress($accountName = null): string {
        $sdk = $this->getSDK($this->option('wallet_id'));
        $response = $sdk->createWalletAddress(AddressType::P2SH_DEPOSIT, 'Deposit wallet: @'.auth('sanctum')->user()->_id.' ('.auth('sanctum')->user()->name.')');

        $webhooks = $sdk->listWalletWebhooks();
        if(!isset($webhooks['webhooks']) || count($webhooks['webhooks']) == 0) $sdk->addWalletWebhook(secure_url('/api/bitgoWebhook'), 'transfer', 0);

        return $response['address'];
    }

    protected function options(): array {
        return [
            new class extends WalletOption {
                function id() {
                    return "wallet_id";
                }
                function name(): string {
                    return "BitGo Wallet ID";
                }
            },
            new class extends WalletOption {
                public function id() {
                    return "api_key";
                }
                public function name(): string {
                    return "BitGo API key";
                }
            },
            new class extends WalletOption {
                public function id() {
                    return "otp";
                }
                public function name(): string {
                    return "BitGo OTP password (2FA, set as 000000)";
                }
            }
        ];
    }

    function isRunning(): bool {
        try {
            $json = json_decode(file_get_contents('http://localhost:3080/api/v2/ping'), true);
            return $json['status'] === 'service is ok!';
        } catch (\Exception $e) {
            return false;
        }
    }

    function process(string $wallet = null) {
        $sdk = $this->getSDK();
        $payload = $sdk->getWebhookPayload();
        $txDetails = $this->getSDK($payload['wallet'])->getWalletTransaction($payload['hash']);

        if (isset($txDetails['error'])) return;

        if (!isset($txDetails['fromWallet'])) {
            $to = null;
            $value = null;

            foreach ($txDetails['outputs'] as $output) {
                if(isset($output['wallet'])) {
                    $to = $output['address'];
                    $value = BitGoSDK::toBTC($output['value']);
                    break;
                }
            }

            if(!$to) return;

            $this->accept($txDetails['confirmations'], $to, $txDetails['normalizedTxHash'], $value);
        }
    }

    function send(string $from, string $to, float $sum) {
        $this->getClient()->sendTransaction($to, BitGoSDK::toSatoshi($sum), env('BITGO_PASSPHRASE'));
    }

    function setupWallet() {}

    function coldWalletBalance(): float {
        return -1;
    }

    function hotWalletBalance(): float {
        return -1;
    }

    public function getClient() {
        $bitgo = $this->allowCoins('BitGoExpress', 'localhost', 3080, $this->getCurrencyCode());

        $bitgo->walletId = $this->option('wallet_id');
        $bitgo->accessToken = $this->option('api_key');
        return $bitgo;
    }

    public function getSDK($walletId = null) {
        $bitgo = $this->allowCoins('BitGoSDK', $this->option('api_key'), $this->getCurrencyCode(), false);
        $bitgo->unlockSession($this->option('otp'));
        $bitgo->accessToken = $this->option('api_key');
        $bitgo->walletId = $walletId;
        return $bitgo;
    }

    /**
     * Fixes outdated BitGo library to allow some new coins
     * @param $instance string BitGoSDK|BitGoExpress
     * @return mixed BitGoSDK|BitGoExpress
     * @throws \ReflectionException
     */
    private function allowCoins(string $instance, $o1, $o2, $o3) {
        $ref = new ReflectionClass('neto737\\BitGoSDK\\'.$instance);
        $o = $ref->newInstanceWithoutConstructor();

        $property = $ref->getProperty('allowedCoins');
        $property->setAccessible(true);
        $property->setValue($o, $this->allowedCoins);

        $o->__construct($o1, $o2, $o3);
        return $o;
    }

}
