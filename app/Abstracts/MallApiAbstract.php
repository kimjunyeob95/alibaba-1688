<?php

namespace App\Abstracts;

use App\Constants\MallErrorMessageConstant;
use App\Models\ApiUser;
use App\Packages\JwtPackage;
use Carbon\Carbon;
use Exception;

abstract class MallApiAbstract
{
    protected array $returnMsg;
    protected JwtPackage $jwtPackage;
    protected string $channel;

    public function __construct(JwtPackage $jwtPackage, string $channel)
    {
        $this->returnMsg  = helpers_fail_message();
        $this->jwtPackage = $jwtPackage;
        $this->channel    = $channel;
    }

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
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func productRegist
     * @description '상품등록'
     */
    abstract function productRegist(): array;

    /**
     * @func orderInfo
     * @description '주문 조회'
     * @param int $orderId
     * @return array
    */
    abstract function orderInfo(int $orderId): array;

    /**
     * @func orderCreate
     * @description '주문 생성'
     * @param array $params
     * @return array
     */
    abstract function orderCreate(array $params): array;
}
