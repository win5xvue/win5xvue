<?php namespace App\Currency\Native;

use Nbobtc\Command\Command;

abstract class V16RPCBitcoin extends BitcoinCurrency {

    private function balance($account): float {
        try {
            $client = $this->getClient();
            $command = new Command('getbalance', $account);
            $response = $client->sendCommand($command);
            $contents = json_decode($response->getBody()->getContents(), true);
            return $contents['result'];
        } catch (\Exception $e) {
            return -1;
        }
    }

    public function coldWalletBalance(): float {
        return $this->balance('deposit');
    }

    public function hotWalletBalance(): float {
        return $this->balance('withdraw');
    }

    public function isRunning(): bool {
        return $this->coldWalletBalance() != -1;
    }

    public function send(string $from, string $to, float $sum) {
        $client = $this->getClient();

        $fee = floatval($this->option('fee'));
        $client->sendCommand(new Command('settxfee', [$fee]));

        $account = json_decode($client->sendCommand(new Command('getaccount', $from))->getBody()->getContents())->result;
        $client->sendCommand(new Command('sendfrom', [$account, $to, $sum - $fee]));
    }

}
