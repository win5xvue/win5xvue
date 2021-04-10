<?php namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Ethereum extends BitGoCurrency {

    function id(): string {
        return "bg_eth";
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

    function style(): string {
        return "#627eea";
    }

    public function getCurrencyCode() {
        return env('APP_DEBUG') ? CurrencyCode::ETHEREUM_TESTNET : CurrencyCode::ETHEREUM;
    }

}
