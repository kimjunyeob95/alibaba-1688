<?php

namespace App\Abstracts;

use App\Constants\MallErrorMessageConstant;
use App\Constants\OrderErrorMessageConstant;
use App\Constants\ProductErrorMessageConstant;
use App\Models\ApiUser;
use App\Models\OrderData;
use App\Models\OrderDetailData;
use App\Models\ProductData;
use App\Models\ProductOptionData;
use App\Packages\JwtPackage;
use App\Vo\Order\OrderDetailDto;
use App\Vo\Order\OrderDto;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

abstract class MallApiAbstract
{
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
        $this->returnMsg        = helpers_fail_message();
        $this->jwtPackage       = $jwtPackage;
        $this->channel          = $channel;
        $this->orderW1          = $orderW1;
        $this->transApiAbstract = $transApiAbstract;
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
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func productRegist
     * @description '상품등록'
     * @param array $offerIds
     * @param string $type
     * @return array
     */
    abstract function productRegist(array $offerIds, string $type): array;

    /**
     * @func orderInfo
     * @description '주문 조회'
     * @param string $orderId
     * @return array
    */
    public function orderInfo(string $orderId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $orderObj = OrderData::where([
                "channel"  => $this->channel,
                "order_id" => $orderId,
            ]);

            if( $orderObj == null ){
                throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER"));
            }

            $result = $this->orderW1->getWOrder($orderId);
            if( $result["isSuccess"] === true && isset($result["data"]["result"]) ){
                $returnMsg = helpers_success_message($result["data"]["result"]);
            } else {
                $returnMsg = helpers_fail_message($result["msg"]);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func orderCreate
     * @description '주문 생성'
     * @param array $params
     * @return array
     */
    public function orderCreate(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $offerId              = $params["offer_id"];
            $optionParamList      = $params["optionParamList"];
            $optionPrice          = $params["option_price"];
            $buyerName            = $params["buyer_name"];
            $buyerClearanceNumber = $params["buyer_clearance_number"];
            $buyerNumber          = $params["buyer_number"];
            $buyerPhone           = $params["buyer_phone"];
            $buyerZipcode         = $params["buyer_zipcode"];
            $buyerAddress         = $params["buyer_address"];
            $buyerMemo            = $params["buyer_memo"];
            $totalQuantity        = 0;

            $orderDetailDtos = [];

            $prdObj = ProductData::where("offer_id", $offerId)->first();
            if( $prdObj == null ){
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));   
            }
            foreach ($optionParamList as &$option) {
                $optionId = $option["option_id"];
                $quantity = $option["quantity"];

                $optObj = ProductOptionData::where([
                    "id"       => $optionId,
                    "offer_id" => $offerId,
                ])->first();
                if( $optObj == null ){
                    throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("OPTION"));
                }

                if( $quantity < 1 ){
                    throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("OPTION_QUANTITY"));
                }
                $option["specId"] = $optObj->spec_id;
                $totalQuantity += $quantity;

                $orderDetailDtoBind = [
                    "order_id"             => 0,
                    "option_id"            => $optObj->id,
                    "quantity"             => $quantity,
                    "origin_option_price"  => $optObj->option_price,
                    "channel_option_price" => $optionPrice,
                ];
                $orderDetailDto = new OrderDetailDto();
                $orderDetailDto->bind($orderDetailDtoBind);

                $orderDetailDtos[] = $orderDetailDto;
            }

            if( $prdObj->start_quantity > $totalQuantity ){
                $startQuantity = $prdObj->start_quantity;
                $errMsg = OrderErrorMessageConstant::getFitErrorMessage("START_QUANTITY") . " 최수 구매 수량: {$startQuantity} | 요청 수량: {$totalQuantity}";
                throw new Exception($errMsg);
            }

            $payload = [
                "offerId"         => $offerId,
                "optionParamList" => $optionParamList
            ];
            $result = $this->orderW1->createWOrder($payload);

            if( $result["isSuccess"] === true && isset($result["data"]["orderId"]) ){
                $orderId = $result["data"]["orderId"];

                try {
                    DB::beginTransaction();

                    $orderDtoBind = [
                        "order_id"               => $orderId,
                        "offer_id"               => $offerId,
                        "channel"                => $this->channel,
                        "buyer_name"             => $buyerName,
                        "buyer_clearance_number" => $buyerClearanceNumber,
                        "buyer_number"           => $buyerNumber,
                        "buyer_phone"            => $buyerPhone,
                        "buyer_zipcode"          => $buyerZipcode,
                        "buyer_address"          => $buyerAddress,
                        "buyer_memo"             => $buyerMemo,
                        "total_quantity"         => $totalQuantity,
                        "total_price"            => $totalQuantity * $optionPrice
                    ];
                    $orderDto = new OrderDto();
                    $orderDto->bind($orderDtoBind);

                    $odProperties = $orderDto->getAllProperties();
                    OrderData::create($odProperties);

                    foreach ($orderDetailDtos as $orderDetailDto) {
                        $orderDetailDto->order_id = $orderId;

                        $oddProperties = $orderDetailDto->getAllProperties();
                        OrderDetailData::create($oddProperties);
                    }
    
                    DB::commit();

                    $returnPayload = [
                        "orderId" => $orderId,
                        "success" => $result["data"]["success"],
                    ];

                    $returnMsg = helpers_success_message($returnPayload);
                } catch (Exception $ee) {
                    DB::rollBack();
                    $returnMsg = helpers_fail_message($ee->getMessage());
                }
            } else {
                $returnMsg = helpers_fail_message($result["msg"]);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func categoryMapping
     * @description '카테고리 매핑'
     * @return array
     */
    abstract function categoryMapping(): array;

    /**
     * @func sendModiProduct
     * @description '수정 된 상품 전송'
     * @return void
    */
    abstract function sendModiProduct(): void;

    /**
     * @func imgTransRequest
     * @description '이미지 번역 요청'
     * @param array $params
     * @return array
    */
    public function imgTransRequest($params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $result = $this->transApiAbstract->channelImgTransRequest($this->channel, $params);
            if( $result["isSuccess"] === true && isset($result["data"]) ){
                $returnMsg = helpers_success_message($result["data"]);
            } else {
                $returnMsg = helpers_fail_message($result["msg"]);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func imgTrans
     * @description '번역 된 이미지 처리'
     * @param array $params
     * @return array
    */
    public function imgTrans($params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $result = $this->transApiAbstract->channelImgTrans($this->channel, $params);
            if( $result["isSuccess"] === true && isset($result["data"]) ){
                $returnMsg = helpers_success_message($result["data"]);
            } else {
                $returnMsg = helpers_fail_message($result["msg"]);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func imgUpload
     * @description '이미지 S3 upload'
     * @param array $params
     * @return array
    */
    public function imgUpload($params): array
    {
        return $this->transApiAbstract->imgUpload($params);
    }
}
