<?php namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Ripple extends BitGoCurrency {

    function id(): string {
        return "bg_xrp";
    }

    public function walletId(): string {
        return "xrp";
    }

    function name(): string {
        return "XRP";
    }

    public function alias(): string {
        return "ripple";
    }

    public function displayName(): string {
        return "Ripple";
    }

    function icon(): string {
        return "xrp";
    }

    function style(): string {
        return "white";
    }

    public function getCurrencyCode() {
        return env('APP_DEBUG') ? CurrencyCode::RIPPLE_TESTNET : CurrencyCode::RIPPLE;
    }

}
