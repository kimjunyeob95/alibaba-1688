<?php

namespace App\Providers;

use App\Abstracts\ApiModuleAbstract;
use Illuminate\Support\ServiceProvider;
use App\Services\Service1688;
use Illuminate\Support\Facades\Route;

class ApiModuleProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ApiModuleAbstract::class, function ($app) {
            $routeName = Route::currentRouteName();

            if(strpos($routeName, "1688") !== false){
                return app(Service1688::class);
            } else {
                return app(Service1688::class);
            }
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
