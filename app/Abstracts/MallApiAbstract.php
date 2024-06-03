<?php

namespace App\Abstracts;

use App\Constants\MallConstant;
use App\Constants\MallErrorMessageConstant;
use App\Models\ApiUser;
use App\Models\OnchannelProductDetailLog;
use App\Packages\JwtPackage;
use App\Traits\MallCategoryTrait;
use App\Traits\MallImageTrait;
use App\Traits\MallOrderTrait;
use Carbon\Carbon;
use Exception;

abstract class MallApiAbstract
{
    use MallCategoryTrait, MallOrderTrait, MallImageTrait;

    protected array $returnMsg;
    protected JwtPackage $jwtPackage;
    protected string $channel;
    protected OrderAbstract $orderW1;
    private TransApiAbstract $transApiAbstract;
    
    public function __construct(
        JwtPackage $jwtPackage,
        string $channel,
        OrderAbstract $orderW1,
        TransApiAbstract $transApiAbstract
    )
    {
        $this->initCategoryTrait($channel);
        $this->initOrderTrait($channel, $orderW1);
        $this->initImageTrait($channel, $transApiAbstract);
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

    /****************************************** 상품 end **********************************************/
}
