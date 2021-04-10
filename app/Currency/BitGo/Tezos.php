<?php namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Tezos extends BitGoCurrency {

    function id(): string {
        return "bg_xtz";
    }

    public function walletId(): string {
        return "xtz";
    }

    function name(): string {
        return "XTZ";
    }

    public function alias(): string {
        return "tezos";
    }

    public function displayName(): string {
        return "Tezos";
    }

    function icon(): string {
        return "xtz";
    }

    function style(): string {
        return "#2c7df7";
    }

    public function getCurrencyCode() {
        return "xtz";
    }

}
