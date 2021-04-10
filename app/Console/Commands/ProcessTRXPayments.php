<?php

namespace App\Console\Commands;

use App\Currency\Currency;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessTRXPayments extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'win5x:processTrxPayments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check recent payments';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $currency = Currency::find('native_trx');
        User::chunk(100, function($users) use($currency) {
            foreach($users as $user) {
                $wallet = $user->makeVisible(['wallet_trx'])->toArray()['wallet_trx'] ?? null;
                if($wallet == null) continue;
                try {
                    $currency->process($wallet);
                } catch (\Exception $e) {
                    Log::critical($e);
                }
            }
        });
    }

}
