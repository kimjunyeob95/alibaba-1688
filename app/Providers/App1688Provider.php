<?php

namespace App\Providers;

use App\Packages\S3;
use App\Services\Category\CategoryW1;
use App\Services\GenuioService;
use App\Services\Product\ProductW1;
use App\Services\Product\ProductW2;
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

        $this->app->bind(ProductW2::class, function ($app) {
            $transApiAbstract = $app->make(GenuioService::class);
            $uploadAbstract   = $app->make(S3::class);

            return new ProductW2($transApiAbstract, $uploadAbstract);
        });

        $this->app->bind(ProductW1::class, function ($app) {
            $transApiAbstract = $app->make(GenuioService::class);
            $uploadAbstract   = $app->make(S3::class);
            $productAbstract  = $app->make(ProductW2::class);

            return new ProductW1($transApiAbstract, $uploadAbstract, $productAbstract);
        });


        $this->app->bind(Service1688Category::class, function ($app) {
            $categoryAbstract  = $app->make(CategoryW1::class);

            return new Service1688Category($categoryAbstract);
        });

        $this->app->bind(Service1688Product::class, function ($app) {
            $productAbstract  = $app->make(ProductW1::class);
            $productAbstract2 = $app->make(ProductW2::class);

            return new Service1688Product($productAbstract, $productAbstract2);
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
