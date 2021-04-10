<?php namespace App\Currency\Local;

use App\Currency\Option\WalletOption;

class Rub extends LocalCurrency {

    function id(): string {
        return "local_rub";
    }

    function walletId(): string {
        return "rub";
    }

    function name(): string {
        return "RUB";
    }

    function alias(): string {
        return "rub";
    }

    function displayName(): string {
        return "RUB";
    }

    function icon(): string {
        return "rub";
    }

    protected function options(): array {
        return [
            new class extends WalletOption {
                function id() {
                    return "fk_merchant_id";
                }
                function name(): string {
                    return "Freekassa Merchant id";
                }
            },
            new class extends WalletOption {
                function id() {
                    return "fk_secret1";
                }

                function name(): string {
                    return "Freekassa secret word 1";
                }
            },
            new class extends WalletOption {
                function id() {
                    return "fk_secret2";
                }

                function name(): string {
                    return "Freekassa secret word 2";
                }
            },
            new class extends WalletOption {
                public function id() {
                    return "min_deposit";
                }
                public function name(): string {
                    return "Min deposit";
                }
            }
        ];
    }

    public function tokenPrice() {
        return 1;
    }

}
