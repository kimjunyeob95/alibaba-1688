<?php

namespace App\Providers;

use App\Models\ExchangeRateHistory;
use Illuminate\Support\Facades\Config;
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
        /** 환율 config 설정 */
        $latestObj = ExchangeRateHistory::orderBy("date", "desc")->first();
        if ($latestObj != null) {
            Config::set('1688_EXCHANGE_RATE', $latestObj->exchange_rate);
        }
    }
}
