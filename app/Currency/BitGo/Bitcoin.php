<?php namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Bitcoin extends BitGoCurrency {

    function id(): string {
        return "bg_btc";
    }

    public function walletId(): string {
        return "btc";
    }

    function name(): string {
        return "BTC";
    }

    public function alias(): string {
        return "bitcoin";
    }

    public function displayName(): string {
        return "Bitcoin";
    }

    function icon(): string {
        return "btc-icon";
    }

    function style(): string {
        return "#f7931a";
    }

    public function getCurrencyCode() {
        return env('APP_DEBUG') ? CurrencyCode::BITCOIN_TESTNET : CurrencyCode::BITCOIN;
    }

}
