<?php
namespace App\Console\Commands;

use App\Constants\CategoryConstant;
use App\Constants\ProductConstant;
use App\Models\ProductData;
use App\Models\ProductOptionData;
use App\Models\ProductWeightData;
use App\Services\Service1688Product;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Psr\Log\LogLevel;

class UpdateWeightDelivery extends Command
{
    protected $signature   = 'update_weight_delivery';
    protected $description = '중량별 배송비';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }
    /*
     * 실행 구문 
     * php artisan update_weight_delivery
    */
    public function handle()
    {
        $perPage = 900;

        $builder    = ProductOptionData::select('offer_id', DB::raw('MAX(weight) as max_weight'))->groupBy("offer_id");
        $totalCount = $builder->get()->count();
        $totalPages = ceil($totalCount / $perPage);
        $weights    = CategoryConstant::WEIGHTS;

        try {
            for ($page = 1; $page <= $totalPages; $page++) {
                Paginator::currentPageResolver(function () use ($page) {
                    return $page;
                });
            
                // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
                $pagedData = $builder->paginate($perPage);
                $results   = $pagedData->items();
    
                foreach ($results as $obj) {
                    $errorFlag = false;
                    $offerId   = $obj->offer_id;
                    $weight    = $obj->max_weight;
                    $weight    = (int)ceil($weight);
        
                    $delivery_price = ProductConstant::WEIGHT_STATUS_NONE_PRICE;
                    $weight_type    = ProductConstant::WEIGHT_STATUS_NONE;
    
                    if( $weight > 0 && $weight <= 100 ){
                        // 1. 상품 배송비
                        $delivery_price = $weights[$weight];
                        $weight_type    = ProductConstant::WEIGHT_STATUS_PRODUCT;
                    } else if( $weight == 0 ) {
                        $cateWeightObj = ProductData::select(["b.*"])->join("category_weight_datas as b", "product_datas.category_id", "=", "b.category_id")
                        ->where("offer_id", $offerId)->first();
    
                        if( $cateWeightObj != null ){
                            // 2. 표준 배송비
                            $weight         = (int)$cateWeightObj->weight;
                            $delivery_price = $weights[$weight];
                            $weight_type    = ProductConstant::WEIGHT_STATUS_CATEGORY;
                        } else if( $cateWeightObj == null ){
                            // 3. 대표 배송비
                            $delivery_price = ProductConstant::WEIGHT_STATUS_NONE_PRICE;
                            $weight_type    = ProductConstant::WEIGHT_STATUS_NONE;
                        }
                    } else {
                        $errorFlag = true;
                        ProductData::where("offer_id", $offerId)->update([
                            "status" => ProductConstant::PRD_STATUS_EXCEPT
                        ]);
                        // $msg       = "offerId: {$offerId} | weight: {$weight} 중량 100 초과 ";
                        // debug_log($msg, "cron/UpdateWeightDelivery", "UpdateWeightDelivery", LogLevel::ERROR);
                    }
    
                    if( $errorFlag === false ){
                        ProductWeightData::updateOrCreate([
                            "offer_id" => $offerId,
                        ],[
                            "weight_type"    => $weight_type,
                            "weight"         => $weight,
                            "delivery_price" => $delivery_price,
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            $msg      = "에러 발생: {$errorMsg}";
            debug_log($msg, "cron/UpdateWeightDelivery", "UpdateWeightDelivery", LogLevel::ERROR);
        }
    }
}
