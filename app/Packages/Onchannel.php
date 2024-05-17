<?php

namespace App\Packages;

use App\Abstracts\MallApiAbstract;
use App\Constants\MallConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Models\CategoryMapping;
use App\Models\ProductModiData;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class Onchannel extends MallApiAbstract
{
    public function __construct(string $channel)
    {
        parent::__construct(app(JwtPackage::class), $channel);
    }

    /**
     * @func productRegist
     * @description '상품등록'
     * @param array $offerIds
     * @param string $type
     * @return array
    */
    public function productRegist(array $offerIds, string $type = WConstant::WAPP_W1): array
    {
        $successIds = [];
        $failIds    = [];
        $updateIds  = [];

        foreach ($offerIds as $offerId) {
           
        }

        $result = ["success" => $successIds, "fail" => $failIds];

        return helpers_success_message($result);
    }

    /**
     * @func productModi
     * @description '상품수정'
     * @param array $offerIds
     * @param string $type
     * @return array
    */
    public function productModi(array $offerIds, string $type):array
    {
        $successIds = [];
        $failIds    = [];

        foreach ($offerIds as $offerId) {

        }

        $result = ["success" => $successIds, "fail" => $failIds];

        return helpers_success_message($result);
    }

    public function orderInfo(int $orderId): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $returnMsg = helpers_success_message(["orderId" => $orderId]);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function orderCreate(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $returnMsg = helpers_success_message($params);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func categoryMapping
     * @description '카테고리 매핑 저장'
     *
     * @return array
     */
    public function categoryMapping(): array
    {
        $returnMsg = $this->returnMsg;
        try {
            DB::beginTransaction();

            $filePath = public_path('app/oc_categories.txt');
            if (File::exists($filePath)) {
                $lines = File::lines($filePath);
                foreach($lines as $line){
                    $data = explode(',', $line);
                    $categoryObj = CategoryMapping::where("mapping_channel", ProductConstant::MAPPING_WAPP)
                        ->where("mapping_code",$data[0])
                        ->get();
                    foreach($categoryObj as $cate){
                        //이지셀 카테고리 매핑
                        CategoryMapping::updateOrCreate([
                                "category_id"     => $cate->category_id,
                                "mapping_channel" => MallConstant::MALL_ONCHANNEL
                            ],[
                                "mapping_code" => $data[1]
                            ]);

                        //이지셀 해외카테고리 매핑
                        CategoryMapping::updateOrCreate([
                                "category_id"     => $cate->category_id,
                                "mapping_channel" => MallConstant::MALL_ONCHANNEL
                            ],[
                                "mapping_code" => $data[2]
                            ]);
                    }
                }
            } else {
                throw new Exception("파일이 존재하지 않습니다.");
            }
            $returnMsg = helpers_success_message();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func sendModiProduct
     * @description '수정 된 상품 전송'
     * @return void
     */
    public function sendModiProduct(): void
    {
        $now      = Carbon::now();
        $modiObjs = ProductModiData::where("is_send", ProductConstant::IS_SEND_N)
        ->where("channel", MallConstant::MALL_ONCHANNEL)
        ->groupBy("offer_id")
        ->get();
        
    }
}
