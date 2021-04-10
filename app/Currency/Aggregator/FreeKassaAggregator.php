<?php namespace App\Currency\Aggregator;

use App\Currency\Currency;
use App\Invoice;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;

class FreeKassaAggregator extends Aggregator {

    private function paymentId(Invoice $invoice) {
        switch ($invoice->method) {
            case 'Test': return 0;
            case 'Qiwi': return 63;
            case 'Yandex Money': return 45;
            case 'VISA / Mastercard': return 160;
            case 'Megafon': return 82;
            case 'MTS': return 84;
            case 'Beeline': return 83;
            case 'Tele2': return 132;
            case 'Payeer': return 114;
            case 'Advcash': return 150;
            case 'PerfectMoney': return 64;
            case 'EXMO': return 180;
            case 'Zcash': return 165;
        }
        return null;
    }

    public function invoice(Invoice $invoice): string {
        $currency = Currency::find('local_rub');
        return 'http://www.free-kassa.ru/merchant/cash.php?'.http_build_query([
                'm' => $currency->option('fk_merchant_id'),
                'oa' => $invoice->sum,
                'o' => $invoice->_id,
                's' => md5($currency->option('fk_merchant_id').':'.$invoice->sum.':'.$currency->option('fk_secret1').':'.$invoice->_id),
                'lang' => 'ru',
                'i' => $this->paymentId($invoice)
            ]);
    }

    public function validate(Request $request): bool {
        return $request->MERCHANT_ORDER_ID != null;
    }

    public function status(Request $request): string {
        $currency = Currency::find('local_rub');
        $sign = md5($currency->option('fk_merchant_id').':'.$request->AMOUNT.':'.$currency->option('fk_secret2').':'.$request->MERCHANT_ORDER_ID);
        if($sign !== $request->SIGN) return 'Sign error';

        $invoice = Invoice::where('_id', $request->MERCHANT_ORDER_ID)->first();
        if($invoice == null) return 'Unknown invoice id';
        if($invoice->status != 0) return 'Already paid';

        $user = User::where('_id', $invoice->user)->first();
        $user->balance($currency)->add($invoice->sum, Transaction::builder()->message('Deposit')->get());
        $invoice->update([
            'status' => 1
        ]);

        if($user->referral) {
            $referrer = User::where('_id', $user->referral)->first();

            $commissionPercent = 0;

            switch ($referrer->vipLevel()) {
                case 0: $commissionPercent = 1; break;
                case 1: $commissionPercent = 2; break;
                case 2: $commissionPercent = 3; break;
                case 3: $commissionPercent = 5; break;
                case 4: $commissionPercent = 10; break;
                case 5: $commissionPercent = 20; break;
            }

            if($commissionPercent !== 0) {
                $commission = ($commissionPercent * $invoice->sum) / 100;
                $referrer->balance(Currency::find($invoice->currency))->add($commission, Transaction::builder()->message('Affiliate commission (' . $commissionPercent . '% from ' . $invoice->sum . ' .' . $this->name() . ')')->get());
            }
        }

        return 'YES';
    }

    function id(): string {
        return 'freekassa';
    }

    function name(): string {
        return 'Free-Kassa';
    }

    function icon(): string {
        return asset('/img/payment/freekassa.svg');
    }

}
