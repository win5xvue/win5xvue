<?php namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class BitcoinCash extends BitGoCurrency {

    function id(): string {
        return "bg_bch";
    }

    public function walletId(): string {
        return "bch";
    }

    function name(): string {
        return "BCH";
    }

    public function alias(): string {
        return "bitcoin-cash";
    }

    public function displayName(): string {
        return "Bitcoin Cash";
    }

    function icon(): string {
        return "bch";
    }

    function style(): string {
        return "#8dc351";
    }

    public function getCurrencyCode() {
        return env('APP_DEBUG') ? CurrencyCode::BITCOIN_CASH_TESTNET : CurrencyCode::BITCOIN_CASH;
    }

}
