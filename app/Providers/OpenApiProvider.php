<?php

namespace App\Providers;

use App\Abstracts\MallApiAbstract;
use App\Abstracts\TransApiAbstract;
use App\Constants\MallConstant;
use App\Constants\TransApiConstant;
use App\Packages\EasySell;
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
        // EasySell 싱글톤으로 등록
        $this->app->singleton(EasySell::class, function () {
            return new EasySell(MallConstant::MALL_EASYSELL);
        });

        // JwtPackage 싱글톤으로 등록
        $this->app->singleton(JwtPackage::class, function () {
            return new JwtPackage();
        });

        // S3 싱글톤으로 등록
        $this->app->singleton(S3::class, function () {
            return new S3();
        });


        /**
         * channel API 의존성 설정
         * start
         * 
        */
        $this->app->bind(MallApiAbstract::class, function ($app) {
            $currentUrl = $app['request']->fullUrl();
            if(strpos($currentUrl, MallConstant::MALL_EASYSELL) !== false){
                return app(EasySell::class);
            };
        });
        /**
         * channel API 의존성 설정
         * end
         * 
        */

        /**
         * Genuio API 의존성 설정
         * start
         * 
        */
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
        /**
         * Genuio API 의존성 설정
         * end
         * 
        */
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
