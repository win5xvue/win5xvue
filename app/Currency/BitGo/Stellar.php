<?php namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Stellar extends BitGoCurrency {

    function id(): string {
        return "bg_xlm";
    }

    public function walletId(): string {
        return "xlm";
    }

    function name(): string {
        return "XLM";
    }

    public function alias(): string {
        return "stellar";
    }

    public function displayName(): string {
        return "Stellar";
    }

    function icon(): string {
        return "xlm";
    }

    function style(): string {
        return "white";
    }

    public function getCurrencyCode() {
        return env('APP_DEBUG') ? CurrencyCode::RIPPLE_TESTNET : CurrencyCode::RIPPLE;
    }

}
