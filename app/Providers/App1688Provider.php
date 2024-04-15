<?php

namespace App\Providers;

use App\Packages\S3;
use App\Services\Category\CategoryV1;
use App\Services\GenuioService;
use App\Services\Product\ProductV1;
use App\Services\Service1688Category;
use Illuminate\Support\ServiceProvider;
use App\Services\Service1688Product;

class App1688Provider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // S3 싱글톤으로 등록
        $this->app->singleton(S3::class, function () {
            return new S3();
        });

        $this->app->bind(ProductV1::class, function ($app) {
            $transApiAbstract = $app->make(GenuioService::class);
            $uploadAbstract   = $app->make(S3::class);

            // ProductV1 인스턴스 생성 시, GenuioService 구현체를 주입
            return new ProductV1($transApiAbstract, $uploadAbstract);
        });

        $this->app->bind(Service1688Category::class, function ($app) {
            $categoryAbstract  = $app->make(CategoryV1::class);

            return new Service1688Category($categoryAbstract);
        });

        $this->app->bind(Service1688Product::class, function ($app) {
            $productAbstract  = $app->make(ProductV1::class);

            return new Service1688Product($productAbstract);
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
