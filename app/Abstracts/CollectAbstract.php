<?php

namespace App\Abstracts;

use App\Constants\CategoryConstant;
use App\Constants\CollectErrorMessageConstant;
use App\Constants\Constant1688;
use App\Constants\LogConstant;
use App\Constants\MallConstant;
use App\Constants\ProductConstant;
use App\Models\CollectPalletLog;
use App\Models\ProductCollectPalletData;
use App\Models\ProductData;
use App\Models\ProductWeightData;
use Carbon\Carbon;
use Exception;

abstract class CollectAbstract
{
    protected array $returnMsg;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    /**
     * @func palletPrdList
     * @description 'WApp 팔레트 수집 조회'
     * @param array $params
     * @return array
    */
    public function palletPrdList(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $palletId  = $params["pallet_id"];
            $beginPage = $params["begin_page"];
            $pageSize  = $params["page_size"];

            $builder = ProductData::select(["product_datas.*", "c.prd_code"])
            ->with([
                "main_img",
                "no_except_options",
                "extends",
                "no_except_notices",
                "category"
            ])
            ->join("product_collect_pallet_datas as b", "product_datas.offer_id", "=", "b.offer_id")
            ->join("onchannel_product_logs as c", "product_datas.offer_id", "=", "c.offer_id")
            ->where("b.pallet_id", $palletId)
            ->where("status", ProductConstant::PRD_STATUS_PUBLISH)
            ->where("c.regist_success", MallConstant::REGIST_Y);

            $lists = $builder->paginate($pageSize, ['*'], 'page', $beginPage);

            $totalRecords = $lists->total();
            $totalPage    = $lists->lastPage();
            $objs         = $lists->items();

            foreach ($objs as $obj) {
                $weightObj = ProductWeightData::where("offer_id", $obj->offer_id)->first();
                foreach ($obj->no_except_options as &$opt) {
                    if( $weightObj != null ){
                        $getWeightDelivery    = getWeightDelivery($weightObj->weight);
                        $ocPrice              = ocPrice($opt->price_1688_option, $getWeightDelivery["shipping_price"]);
                        $opt->option_price    = $ocPrice["option_price"];
                        $opt->onch_price      = $ocPrice["onch_price"];
                        $opt->cus_price       = $ocPrice["cus_price"];
                        $opt->recom_cus_price = $ocPrice["recom_cus_price"];
                    }
                }
            }
            $res = [
                "pallet_id"     => $palletId,
                "begin_page"    => (int)$beginPage,
                "page_size"     => (int)$pageSize,
                "total_records" => (int)$totalRecords,
                "total_page"    => (int)$totalPage,
                "records"       => $objs,
            ];
            $returnMsg = helpers_success_message($res);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function palletValidation(int $palletId): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $result = [
                "validate" => false,
            ];
            $obj = CollectPalletLog::where("pallet_id", $palletId)->first();
            $cnt = ProductCollectPalletData::where("pallet_id", $palletId)->count();
            if( $obj == null ){
                throw new Exception(CollectErrorMessageConstant::getNotHaveErrorMessage("COLLECTPALLETLOG"));   
            }

            $completedAt     = Carbon::parse($obj->completed_at);
            $currentTime     = Carbon::now();
            $hoursDifference = $currentTime->diffInHours($completedAt);

            if( $hoursDifference >= 24 && $obj->status == LogConstant::COLLECT_COMPLETE && $cnt > 0 ){
                $result["validate"] = true;
            }

            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
