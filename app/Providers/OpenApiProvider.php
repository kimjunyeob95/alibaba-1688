<?php

namespace App\Providers;

use App\Abstracts\OpenApiAbstract;
use App\Constants\OpenApiConstant;
use App\Packages\JwtPackage;
use App\Services\GenuioService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class OpenApiProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // JwtPackage 싱글톤으로 등록
        $this->app->singleton(JwtPackage::class, function ($app) {
            return new JwtPackage();
        });

        // GenuioService 싱글톤으로 등록
        $this->app->singleton(GenuioService::class, function ($app) {
            return new GenuioService($app->make(JwtPackage::class));
        });

        $this->app->bind(OpenApiAbstract::class, function ($app) {
            $routeName = Route::currentRouteName();

            if(strpos($routeName, OpenApiConstant::API_USER_COMPANY_GENUIO) !== false){
                return app(GenuioService::class);
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
