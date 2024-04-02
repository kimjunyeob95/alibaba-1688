<?php

namespace App\Providers;

use App\Constants\MallConstant;
use App\Http\Controllers\MallController;
use App\Packages\EasySell;
use App\Services\MallApiService;
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
            return new EasySell();
        });

        $this->app->bind(MallApiService::class, function () {
            $routeName = Route::currentRouteName();
            if(strpos($routeName, MallConstant::MALL_EASYSELL) !== false){
                return app(EasySell::class);
            };
        });

        $this->app->bind(MallController::class, function () {
            return app(MallApiService::class);
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
