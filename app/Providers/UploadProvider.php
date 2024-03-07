<?php

namespace App\Providers;

use App\Abstracts\UploadAbstract;
use App\Services\S3Service;
use App\Services\Service1688;
use Illuminate\Support\ServiceProvider;

class UploadProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // S3Service를 싱글톤으로 등록
        $this->app->singleton(S3Service::class, function ($app) {
            return new S3Service();
        });

        // Service1688 클래스가 UploadAbstract를 필요로 할 때 S3Service 싱글톤을 제공
        $this->app->when(Service1688::class)
                  ->needs(UploadAbstract::class)
                  ->give(S3Service::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
