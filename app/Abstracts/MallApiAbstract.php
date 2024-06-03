<?php

namespace App\Abstracts;

use App\Constants\MallErrorMessageConstant;
use App\Models\ApiUser;
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
     * @param string $type
     * @param string $sendType
     * @return array
     */
    abstract function productRegist(array $offerIds, string $type, string $sendType): array;

    /**
     * @func sendModiProduct
     * @description '수정 된 상품 전송'
     * @return void
    */
    abstract function sendModiProduct(): void;

    /****************************************** 상품 end **********************************************/
}
