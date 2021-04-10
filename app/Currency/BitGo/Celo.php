<?php namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Celo extends BitGoCurrency {

    function id(): string {
        return "bg_celo";
    }

    public function walletId(): string {
        return "celo";
    }

    function name(): string {
        return "CELO";
    }

    public function alias(): string {
        return "celo-dollar";
    }

    public function displayName(): string {
        return "Celo";
    }

    function icon(): string {
        return "celo";
    }

    function style(): string {
        return "#35d07f";
    }

    public function getCurrencyCode() {
        return 'celo';
    }

}
