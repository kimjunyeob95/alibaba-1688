<?php

namespace App\Providers;

use App\Services\Category\CategoryV1;
use App\Services\GenuioService;
use App\Services\Product\ProductV1;
use Illuminate\Support\ServiceProvider;
use App\Services\Service1688;

class App1688Provider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Service1688::class, function ($app) {
            $categoryAbstract = $app->make(CategoryV1::class);
            $productAbstract  = $app->make(ProductV1::class);
            $openApiAbstract  = $app->make(GenuioService::class);

            // Service1688 인스턴스 생성 시, 구현체를 주입
            return new Service1688($categoryAbstract, $productAbstract, $openApiAbstract);
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
