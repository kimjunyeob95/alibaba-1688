<?php
namespace App\Console\Commands;

use App\Abstracts\ProductAbstract;
use App\Constants\ForbiddenWordConstant;
use App\Models\ForbiddenWordData;
use App\Models\ProductData;
use App\Models\ProductForbiddenData;
use App\Services\Product\ProductW1;
use App\Services\Service1688Product;
use Illuminate\Console\Command;
use Illuminate\Pagination\Paginator;

class UpdateForbiddenWord extends Command
{
    protected $signature   = 'update_forbidden_word';
    protected $description = '금칙어 사전 적용';

    protected Service1688Product $service1688Product;
    protected ProductAbstract $productAbstract;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
        $this->productAbstract = app(ProductW1::class);
    }
    /*
     * 실행 구문 
     * php artisan update_forbidden_word
    */
    public function handle()
    {
        $perPage = 900;

        $builder = ProductData::query();

        $totalCount = $builder->count();
        $totalPages = ceil($totalCount / $perPage);
        
        $deletePrdForbiddenWords  = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_DELETE)->get();
        $replacePrdForbiddenWords = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_REPLACE)->get();

        for ($page = 1; $page <= $totalPages; $page++) {
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        
            // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
            $pagedData = $builder->paginate($perPage);
            $results   = $pagedData->items();

            // 1. 상품명
            foreach ($results as $prdObj) {

                $forObj = ProductForbiddenData::where([
                    "offer_id"   => $prdObj->offer_id,
                    "apply_type" => ForbiddenWordConstant::KEYWORD_APPLY_TITLE,
                ])->first();
    
                if( $forObj != null ){
                    $prd_name_kr = $forObj->origin_text;
                } else {
                    $prd_name_kr = $prdObj->prd_name_kr;
                }
    
                // 1. 삭제어
                $upText = $this->productAbstract->removeForbiddenText($deletePrdForbiddenWords, $prd_name_kr, ForbiddenWordConstant::KEYWORD_APPLY_TITLE);
                // 2. 교체어
                $upText = $this->productAbstract->replaceForbiddenText($replacePrdForbiddenWords, $upText, ForbiddenWordConstant::KEYWORD_APPLY_TITLE);
                $upText = trim($upText);
                $upText = removeDuplicateWords($upText);
                if( $prd_name_kr != $upText ){
                    ProductForbiddenData::updateOrCreate(
                        [
                            "offer_id"   => $prdObj->offer_id,
                            "apply_type" => ForbiddenWordConstant::KEYWORD_APPLY_TITLE
                        ],
                        [
                            "origin_text" => $prd_name_kr,
                            "trans_text"  => $upText
                        ]
                    );
                    ProductData::where("id", $prdObj->id)->update([
                        "prd_name_kr" => $upText
                    ]);
                }
            }
        }
    }
}
