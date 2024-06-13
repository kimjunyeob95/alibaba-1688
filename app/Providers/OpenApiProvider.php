<?php

namespace App\Providers;

use App\Abstracts\MallApiAbstract;
use App\Abstracts\TransApiAbstract;
use App\Constants\MallConstant;
use App\Constants\TransApiConstant;
use App\Packages\EasySell;
use App\Packages\JwtPackage;
use App\Packages\Onchannel;
use App\Packages\S3;
use App\Services\GenuioService;
use App\Services\Order\OrderW1;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class OpenApiProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /**
         * Genuio API 의존성 설정
         * start
         * 
        */
        $this->app->singleton(GenuioService::class, function ($app) {
            return new GenuioService(
                $app->make(JwtPackage::class),
                $app->make(S3::class)
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

        /** JwtPackage 싱글톤으로 등록 */
        $this->app->singleton(JwtPackage::class, function () {
            return new JwtPackage();
        });

        /** EasySell 싱글톤으로 등록 */
        $this->app->singleton(EasySell::class, function () {
            return new EasySell(app(JwtPackage::class), MallConstant::MALL_EASYSELL, app(OrderW1::class), app(GenuioService::class));
        });
        /** Onchannel 싱글톤으로 등록 */
        $this->app->singleton(Onchannel::class, function () {
            return new Onchannel(app(JwtPackage::class), MallConstant::MALL_ONCHANNEL, app(OrderW1::class), app(GenuioService::class));
        });


        /**
         * channel API 의존성 설정
         * start
         * 
        */
        $this->app->bind(MallApiAbstract::class, function ($app) {
            $currentUrl = $app['request']->fullUrl();

            if(strpos(strtoupper($currentUrl), strtoupper(MallConstant::MALL_EASYSELL)) !== false){
                return app(EasySell::class);
            } else if(strpos(strtoupper($currentUrl), strtoupper(MallConstant::MALL_ONCHANNEL)) !== false){
                return app(Onchannel::class);
            } else {
                return app(Onchannel::class);
            };
        });
        /**
         * channel API 의존성 설정
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
