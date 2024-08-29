<?php

namespace App\Abstracts;

use App\Constants\EasySellConstant;
use App\Constants\MallConstant;
use App\Constants\MallErrorMessageConstant;
use App\Constants\OnchannelConstant;
use App\Exceptions\ArrayValueError;
use App\Models\ApiUser;
use App\Models\EasysellProductDetailLog;
use App\Models\EasysellProductLog;
use App\Models\OnchannelProductDetailLog;
use App\Models\OnchannelProductLog;
use App\Packages\JwtPackage;
use App\Traits\MallCategoryTrait;
use App\Traits\MallImageTrait;
use App\Traits\MallOrderTrait;
use App\Traits\MallProductTrait;
use Carbon\Carbon;
use Exception;

abstract class MallApiAbstract
{
    use MallCategoryTrait, MallOrderTrait, MallImageTrait, MallProductTrait;

    protected array $returnMsg;
    protected JwtPackage $jwtPackage;
    protected string $channel;
    protected OrderAbstract $orderW1;
    private TransApiAbstract $transApiAbstract;
    protected ProductAbstract $productW1;

    public function __construct(
        JwtPackage $jwtPackage,
        string $channel,
        OrderAbstract $orderW1,
        TransApiAbstract $transApiAbstract,
        ProductAbstract $productW1
    )
    {
        $this->initCategoryTrait($channel);
        $this->initOrderTrait($channel, $orderW1);
        $this->initImageTrait($channel, $transApiAbstract);
        $this->initProductTrait($channel, $productW1);
        $this->returnMsg  = helpers_fail_message();
        $this->jwtPackage = $jwtPackage;
        $this->channel    = $channel;
    }

    /****************************************** 토큰 start **********************************************/

    /**
     * @func tokenCreate
     * @description '토큰 생성'
     * @param array $params
     * @return array
    */
    public function tokenCreate(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            if( !isset($params["user_id"]) ){
                throw new Exception(MallErrorMessageConstant::getNotHaveErrorMessage("USER_ID"));
            }
            $userId = $params["user_id"];

            $result = $this->jwtPackage->tokenCreate($userId, $this->channel);
            if( $result["isSuccess"] && isset($result["data"]["token"]) ){
                ApiUser::where([
                    'user_id'      => $userId,
                    'user_company' => $this->channel,
                ])->update([
                    "updated_at" => Carbon::now()
                ]);
                $tokenResult = $result["data"];
                $returnMsg   = helpers_success_message($tokenResult);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /****************************************** 토큰 end **********************************************/

    /****************************************** 상품 start **********************************************/

    /**
     * @func productRegist
     * @description '상품등록'
     * @param array $offerIds
     * @param array $params
     * @return array
     */
    abstract function productRegist(array $offerIds, array $params = []): array;

    /**
     * @func productLog
     * @description '상품전송 로그'
     * @param int $logId
     * @return array
    */
    public function productLog(int $logId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            if( $this->channel == MallConstant::MALL_ONCHANNEL ){
                $builder = OnchannelProductDetailLog::query();
            }else if( $this->channel == MallConstant::MALL_EASYSELL ){
                $builder = EasysellProductDetailLog::query();
            }

            $objs   = $builder->where("log_id", $logId)->orderBy("created_at", "desc")->get();
            $result = [];
            foreach ($objs as $obj) {
                $sendType   = MallConstant::SEND_TYPE_LIST[$obj->send_type];
                $isSuccess  = MallConstant::REGIST_TYPE_LIST[$obj->is_success];
                $message    = $obj->message;
                $created_at = Carbon::parse($obj->created_at)->format('Y-m-d H:i:s');

                $result[] = [
                    "sendType"   => $sendType,
                    "isSuccess"  => $isSuccess,
                    "message"    => $message,
                    "created_at" => $created_at,
                ];
            }

            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func sendModiProduct
     * @description '수정 된 상품 전송'
     * @return void
    */
    abstract function sendModiProduct(): void;

    /**
     * @func productRegistLog
     * @description '모든 채널 상품 등록 전송 로그'
     * @param int $offerId
     * @return array
    */
    public function productRegistLog(int $offerId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $esWLogObj = EasysellProductLog::where([
                "offer_id" => $offerId,
                "w_type" => EasySellConstant::TYPE_W
            ])->first();

            $esDropLogObj = EasysellProductLog::where([
                "offer_id" => $offerId,
                "w_type" => EasySellConstant::TYPE_DROPHUB
            ])->first();


            $ocPublicLogObj = OnchannelProductLog::where([
                "offer_id" => $offerId,
                "send_type" => OnchannelConstant::PRD_CHANNEL,
            ])->first();

            $ocPrivateLogObj = OnchannelProductLog::where([
                "offer_id" => $offerId,
                "send_type" => OnchannelConstant::PRD_CHANNEL_PRIVATE,
            ])->first();

            $result = [
                "esWLogObj"       => $esWLogObj,
                "esDropLogObj"    => $esDropLogObj,
                "ocPublicLogObj"  => $ocPublicLogObj,
                "ocPrivateLogObj" => $ocPrivateLogObj,
            ];

            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func productWappRegist
     * @description 'WApp 상품 생성 후 채널 전송'
     * @param int $offerId
     * @param array $params
     * @return array
    */
    public function productWappRegist(int $offerId, array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $result = $this->productWappRegistTrait($offerId);

            if( $result["isSuccess"] != true ){
                $errArray = [
                    "msg"        => $result["msg"],
                    "error_code" => MallErrorMessageConstant::ERROR_CODE["WAPP"]
                ];
                throw new ArrayValueError($errArray);
            }

            if( $this->channel == MallConstant::MALL_ONCHANNEL ){
                $channelParams = [
                    "sendTypeList" => [
                        $params["channel_type"]
                    ],
                    "endPoint" => "/api/v1/product/regist/1688"
                ];
                $regResult = $this->productRegist([$offerId], $channelParams);
                if( !empty($regResult["data"]["fail"]) ){
                    $errArray = [
                        "msg"        => $regResult["data"]["fail"][0]["msg"],
                        "error_code" => $regResult["data"]["fail"][0]["error_code"]
                    ];
                    throw new ArrayValueError($errArray);
                }
                if( !empty($regResult["data"]["success"][0]) ){
                    $returnMsg = helpers_success_message($regResult["data"]["success"][0]);
                }
            } else if( $this->channel == MallConstant::MALL_EASYSELL ){
                $channelParams = [
                    "type" => [$params["channel_type"]]
                ];
                $regResult = $this->productRegist([$offerId], $channelParams);
                if( !empty($regResult["data"]["fail"]) ){
                    $errArray = [
                        "msg"        => $regResult["data"]["fail"][0]["msg"],
                        "error_code" => MallErrorMessageConstant::ERROR_CODE["ES_API"]
                    ];
                    throw new ArrayValueError($errArray);
                }
                if( !empty($regResult["data"]["success"][0]) ){
                    $returnMsg = helpers_success_message($regResult["data"]["success"][0]);
                }
            } else {
                $errArray = [
                    "msg"        => MallErrorMessageConstant::getNotHaveErrorMessage("CHANNEL"),
                    "error_code" => MallErrorMessageConstant::ERROR_CODE["WAPP"]
                ];
                throw new ArrayValueError($errArray);
            }
        } catch (ArrayValueError $e) {
            $errorArray = $e->getErrorArray();
            $msg        = $errorArray["msg"];
            $returnMsg  = helpers_fail_message($msg, ["error_code" => $errorArray["error_code"]]);
        }

        return $returnMsg;
    }

    /****************************************** 상품 end **********************************************/
}
