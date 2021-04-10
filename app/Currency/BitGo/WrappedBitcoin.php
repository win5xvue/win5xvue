<?php namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class WrappedBitcoin extends BitGoCurrency {

    function id(): string {
        return "bg_wbtc";
    }

    public function walletId(): string {
        return "wbtc";
    }

    function name(): string {
        return "WBTC";
    }

    public function alias(): string {
        return "wrapped-bitcoin";
    }

    public function displayName(): string {
        return "Wrapped Bitcoin";
    }

    function icon(): string {
        return "btc-icon";
    }

    function style(): string {
        return "#eba809";
    }

    public function getCurrencyCode() {
        return "wbtc";
    }

}
