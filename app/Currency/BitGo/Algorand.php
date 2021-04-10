<?php namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Algorand extends BitGoCurrency {

    function id(): string {
        return "bg_algo";
    }

    public function walletId(): string {
        return "algo";
    }

    function name(): string {
        return "ALGO";
    }

    public function alias(): string {
        return "algorand";
    }

    public function displayName(): string {
        return "Algorand";
    }

    function icon(): string {
        return "algorand";
    }

    function style(): string {
        return "white";
    }

    public function getCurrencyCode() {
        return 'algo';
    }

}
