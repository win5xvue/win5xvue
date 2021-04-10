<?php namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Tron extends BitGoCurrency {

    function id(): string {
        return "bg_trx";
    }

    public function walletId(): string {
        return "trx";
    }

    function name(): string {
        return "TRX";
    }

    public function alias(): string {
        return "tron";
    }

    public function displayName(): string {
        return "Tron (TRX)";
    }

    function icon(): string {
        return "trx";
    }

    function style(): string {
        return "#eb0a29";
    }

    public function getCurrencyCode() {
        return 'trx';
    }

}
