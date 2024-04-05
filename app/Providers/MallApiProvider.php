<?php

namespace App\Providers;

use App\Abstracts\MallApiAbstract;
use App\Constants\MallConstant;
use App\Packages\EasySell;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class MallApiProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // EasySell 싱글톤으로 등록
        $this->app->singleton(EasySell::class, function () {
            return new EasySell(MallConstant::MALL_EASYSELL);
        });

        $this->app->bind(MallApiAbstract::class, function () {
            $routeName = Route::currentRouteName();
            if(strpos($routeName, MallConstant::MALL_EASYSELL) !== false){
                return app(EasySell::class);
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
