<?php
namespace App\Console\Commands;

use App\Constants\ExceptConstant;
use App\Models\ProductExceptData;
use App\Models\ProductNoticeData;
use App\Models\WNoticeData;
use App\Services\Service1688Product;
use Illuminate\Console\Command;

class UpdateAttribute extends Command
{
    protected $signature   = 'update_attribute';
    protected $description = '정보고시 적용';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }
    /*
     * 실행 구문 
     * php artisan update_attribute
    */
    public function handle()
    {
        // 1. 정보고시 제외 처리
        $exceptList = ProductExceptData::where([
            "except_type" => ExceptConstant::EXCEPT_NOTICE,
            "is_except"   => ExceptConstant::IS_EXCEPT_Y
        ])->pluck("attribute_id")->toArray();
        if( !empty($exceptList) ){
            ProductNoticeData::whereIn("attribute_id", $exceptList)->update([
                "is_except" => ExceptConstant::IS_EXCEPT_Y
            ]);
        }

        // 2. 정보고시 항목명 수정
        $wNObjs = WNoticeData::where("apply_attribute_name", "!=", "")->groupBy("attribute_id")->get();
        foreach ($wNObjs as $wNObj) {
            ProductNoticeData::where("attribute_id", $wNObj->attribute_id)->update([
                "attribute_name_kr" => $wNObj->apply_attribute_name
            ]);
        }
    }
}
