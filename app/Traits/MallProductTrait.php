<?php

namespace App\Traits;

use App\Abstracts\ProductAbstract;
use App\Constants\MallConstant;
use App\Constants\MallErrorMessageConstant;
use App\Constants\OnchannelConstant;
use App\Models\OnchannelProductDetailLog;
use App\Models\OnchannelProductLog;
use App\Models\OnchProductData;
use App\Models\ProductData;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

trait MallProductTrait
{
    protected array $returnMsg;
    protected string $channel;
    protected ProductAbstract $productW1;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    public function initProductTrait(string $channel, ProductAbstract $productW1): void
    {
        $this->channel   = $channel;
        $this->productW1 = $productW1;
    }

    /**
     * @func productWappRegist
     * @description 'WApp에 상품등록'
     * @param int $offerId
     * @param array $params
     * @return array
    */
    public function productWappRegist(int $offerId, array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $channelType = $params["channelType"];
            $channelCode = $params["channelCode"];

            $result = $this->productW1->collectProductNotLog($offerId);

            if( $result["isSuccess"] === true ){
                DB::beginTransaction();

                if( $this->channel == MallConstant::MALL_ONCHANNEL ){
                    $cnt = OnchannelProductLog::where([
                        "offer_id"       => $offerId,
                        "send_type"      => $channelType,
                        "regist_success" => MallConstant::REGIST_SUCCESS
                    ])->count();

                    if( $cnt == 0 ){
                        $log = OnchannelProductLog::updateOrCreate(
                            [
                                "offer_id"  => $offerId,
                                "member_id" => OnchannelConstant::ONCH1688,
                                "send_type" => $channelType,
                            ],
                            [
                                "prd_code"       => $channelCode,
                                "regist_success" => MallConstant::REGIST_SUCCESS,
                                "message"        => "productWappRegist method insert",
                                "registed_at"    => Carbon::now(),
                            ]
                        );

                        OnchannelProductDetailLog::create([
                            "log_id"     => $log->id,
                            "send_type"  => MallConstant::SEND_TYPE_REGIST,
                            "is_success" => MallConstant::REGIST_SUCCESS,
                            "message"    => ""
                        ]);

                        $returnMsg = helpers_success_message();
                    }
                }

                DB::commit();
            } else {
                throw new Exception(MallErrorMessageConstant::getNotHaveErrorMessage("W_PRD_COLLECT"));
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());

            DB::rollBack();
        }

        return $returnMsg;
    }
}
