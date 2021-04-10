<?php namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Litecoin extends BitGoCurrency {

    function id(): string {
        return "bg_ltc";
    }

    public function walletId(): string {
        return "ltc";
    }

    function name(): string {
        return "LTC";
    }

    public function alias(): string {
        return "litecoin";
    }

    public function displayName(): string {
        return "Litecoin";
    }

    function icon(): string {
        return "ltc";
    }

    function style(): string {
        return "#bfbbbb";
    }

    public function getCurrencyCode() {
        return env('APP_DEBUG') ? CurrencyCode::LITECOIN_TESTNET : CurrencyCode::LITECOIN;
    }

}
