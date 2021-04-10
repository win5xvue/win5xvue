<?php namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class ZCash extends BitGoCurrency {

    function id(): string {
        return "bg_zec";
    }

    public function walletId(): string {
        return "zec";
    }

    function name(): string {
        return "ZEC";
    }

    public function alias(): string {
        return "zcash";
    }

    public function displayName(): string {
        return "ZCash";
    }

    function icon(): string {
        return "zec";
    }

    function style(): string {
        return "white";
    }

    public function getCurrencyCode() {
        return env('APP_DEBUG') ? CurrencyCode::ZCASH_TESTNET : CurrencyCode::ZCASH;
    }

}
