<?php namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class BitcoinGold extends BitGoCurrency {

    function id(): string {
        return "bg_btg";
    }

    public function walletId(): string {
        return "btg";
    }

    function name(): string {
        return "BTG";
    }

    public function alias(): string {
        return "bitcoin-gold";
    }

    public function displayName(): string {
        return "Bitcoin Gold";
    }

    function icon(): string {
        return "btg";
    }

    function style(): string {
        return "#eba809";
    }

    public function getCurrencyCode() {
        return CurrencyCode::BITCOIN_GOLD;
    }

}
