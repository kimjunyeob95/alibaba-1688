<?php

namespace App\Providers;

use App\Abstracts\ApiModuleAbstract;
use App\Packages\S3;
use App\Services\GenuioService;
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
        $this->app->bind(Service1688::class, function ($app) {
            // UploadAbstract와 OpenApiAbstract 구현체 인스턴스 생성
            $uploadAbstract  = $app->make(S3::class);
            $openApiAbstract = $app->make(GenuioService::class);

            // Service1688 인스턴스 생성 시, 구현체를 주입
            return new Service1688($uploadAbstract, $openApiAbstract);
        });

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
