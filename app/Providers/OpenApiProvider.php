<?php

namespace App\Providers;

use App\Abstracts\TransApiAbstract;
use App\Constants\TransApiConstant;
use App\Packages\JwtPackage;
use App\Packages\S3;
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
        $this->app->singleton(JwtPackage::class, function () {
            return new JwtPackage();
        });

        // S3 싱글톤으로 등록
        $this->app->singleton(S3::class, function () {
            return new S3();
        });

        // GenuioService 싱글톤으로 등록
        $this->app->singleton(GenuioService::class, function ($app) {
            return new GenuioService(
                $app->make(JwtPackage::class),
                $app->make(S3::class),
            );
        });

        $this->app->bind(TransApiAbstract::class, function () {
            $routeName = Route::currentRouteName();

            if(strpos($routeName, TransApiConstant::API_USER_COMPANY_GENUIO) !== false){
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
