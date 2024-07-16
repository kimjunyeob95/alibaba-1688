<?php
namespace App\Console\Commands;

use App\Constants\LogConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Models\ProductData;
use App\Services\Product\ProductW1;
use Illuminate\Console\Command;
use Illuminate\Pagination\Paginator;
use Psr\Log\LogLevel;

class MissProductReCollect extends Command
{
    protected $signature   = 'miss_product_re_collect {--wversion=}';
    protected $description = '정보부족 상품 재수집';

    protected ProductW1 $productW1;

    public function __construct(ProductW1 $productW1)
    {
        parent::__construct();

        $this->productW1 = $productW1;
    }
    /*
     * 실행 구문 
     * php artisan miss_product_re_collect --wversion=W1
    */
    public function handle()
    {
        $wversion = $this->option('wversion') ?? WConstant::WAPP_W1;
        $type     = LogConstant::COLLECT_MISS_PRODUCT;

        if( $wversion == WConstant::WAPP_W1 ){
            $builder = ProductData::select(["offer_id"])->where("status", ProductConstant::PRD_STATUS_MISS);

            $perPage    = 900;
            $totalCount = $builder->count();
            $totalPages = ceil($totalCount / $perPage);

            for ($page = 1; $page <= $totalPages; $page++) {
                
                Paginator::currentPageResolver(function () use ($page) {
                    return $page;
                });
                
                // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
                $pagedData = $builder->paginate($perPage);
                $results   = $pagedData->items();
    
                foreach ($results as $obj) {
                    $apiResult = $this->productW1->collectProductNotLog($obj->offer_id);
                    // if( $apiResult["isSuccess"] != true ){
                    //     debug_log($apiResult["msg"], "product/{$type}", $type, LogLevel::ERROR);
                    // }
                }
            }
        }
    }
}
