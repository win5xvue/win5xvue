<?php namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Dash extends BitGoCurrency {

    function id(): string {
        return "bg_dash";
    }

    public function walletId(): string {
        return "dash";
    }

    function name(): string {
        return "DASH";
    }

    public function alias(): string {
        return "dash";
    }

    public function displayName(): string {
        return "Dash";
    }

    function icon(): string {
        return "dash";
    }

    function style(): string {
        return "#2573c2";
    }

    public function getCurrencyCode() {
        return env('APP_DEBUG') ? CurrencyCode::DASH_TESTNET : CurrencyCode::DASH;
    }

}
