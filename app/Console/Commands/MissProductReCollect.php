<?php
namespace App\Console\Commands;

use App\Constants\LogConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Models\ProductData;
use App\Services\Service1688Product;
use Illuminate\Console\Command;

class MissProductReCollect extends Command
{
    protected $signature   = 'miss_product_re_collect {--wversion=}';
    protected $description = '정보부족 상품 재수집';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
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
            $offerIds = ProductData::where("status", ProductConstant::PRD_STATUS_MISS)
            ->where("w_type", WConstant::WAPP_W1)->pluck('offer_id')
            ->toArray();

            if( !empty($offerIds) ){
                $this->service1688Product->collectProduct($offerIds, $type);
            }
        } else if( $wversion == WConstant::WAPP_W2 ){
            $offerIds = ProductData::where("status", ProductConstant::PRD_STATUS_MISS)
            ->where("w_type", WConstant::WAPP_W2)->pluck('offer_id')
            ->toArray();

            if( !empty($offerIds) ){
                $this->service1688Product->collectProductW2($offerIds, $type);
            }
        }
    }
}
