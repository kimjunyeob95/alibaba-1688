<?php

namespace App\Providers;

use App\Models\ExchangeRateHistory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /** 최신 환율 config 등록 */
        $lastestObj = ExchangeRateHistory::orderBy("date", "desc")->first();
        config(['1688_EXCHANGE_RATE' => $lastestObj->exchange_rate]);
    }
}
