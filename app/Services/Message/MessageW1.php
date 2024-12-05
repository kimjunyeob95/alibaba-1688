<?php

namespace App\Services\Message;

use App\Abstracts\OrderAbstract;
use App\Abstracts\WMessageAbstract;
use App\Abstracts\WmsAbstract;
use App\Constants\MessageConstant;
use App\Constants\MessageErrorMessageConstant;
use App\Events\OrderPubSubEvent;
use App\Exceptions\ArrayValueError;
use App\Models\BonaeraInBaseData;
use App\Models\BonaeraInProductData;
use App\Models\OrderBaseData;
use App\Vo\Order\OrderPubSubDto;
use Exception;
use Psr\Log\LogLevel;

class MessageW1 extends WMessageAbstract
{
    private OrderAbstract $orderW1;
    private WmsAbstract $wmsW1;

    public function __construct(
        OrderAbstract $orderW1,
        WmsAbstract $wmsW1,
    )
    {
        parent::__construct();
        $this->orderW1 = $orderW1;
        $this->wmsW1   = $wmsW1;
    }

    /**
    * @func message
    * @description 'W1 메세지 카프카 등록'
    * @param array $params
    * @return array
    */
    public function message(array $params): array
    {
        $returnMsg = $this->returnMsg;
        $message   = [];

        try {
            if( isset($params["message"]) && !empty($params["message"]) && isset($params["_aop_signature"]) && !empty($params["_aop_signature"]) ){
                $message = json_decode($params["message"], JSON_UNESCAPED_UNICODE);

                if( !isset($message["type"]) ){
                    $errArray = [
                        "msg"     => MessageErrorMessageConstant::getNotHaveErrorMessage("TYPE"),
                        "message" => $message
                    ];
                    throw new ArrayValueError($errArray);
                }

                $type    = $message["type"];
                $orderId = "";
                if( in_array($type, MessageConstant::MESSAGE_TYPE_LIST) ){
                    if( isset($message["data"]["orderId"]) && $message["data"]["orderId"] ) {
                        $orderId                    = (string)$message["data"]["orderId"];
                        $message["data"]["orderId"] = $orderId;
                    } else if( isset($message["data"]["OrderLogisticsTracingModel"]["orderLogsItems"]) && count($message["data"]["OrderLogisticsTracingModel"]["orderLogsItems"]) > 0) {
                        $orderId = (string)$message["data"]["OrderLogisticsTracingModel"]["orderLogsItems"][0]["orderId"];
                        foreach ($message["data"]["OrderLogisticsTracingModel"]["orderLogsItems"] as &$items) {
                            $items["orderId"] = $orderId;
                        }
                    } else if( isset($message["data"]["MailNoChangeModel"]["orderLogsItems"]) && count($message["data"]["MailNoChangeModel"]["orderLogsItems"]) > 0) {
                        $orderId = (string)$message["data"]["MailNoChangeModel"]["orderLogsItems"][0]["orderId"];
                        foreach ($message["data"]["MailNoChangeModel"]["orderLogsItems"] as &$items) {
                            $items["orderId"] = $orderId;
                        }
                    }
                    
                    if( $orderId === "" ){
                        $errArray = [
                            "msg"     => MessageErrorMessageConstant::getNotHaveErrorMessage("ORDERID"),
                            "message" => $message
                        ];
                        throw new ArrayValueError($errArray);
                    }

                    $logisticsId = "";
                    if( isset($message["data"]["MailNoChangeModel"]["logisticsId"]) && !empty($message["data"]["MailNoChangeModel"]["logisticsId"]) ) {
                        $logisticsId = $message["data"]["MailNoChangeModel"]["logisticsId"];
                    }

                    $logParams = [
                        "message"        => $message,
                        "_aop_signature" => $params["_aop_signature"],
                    ];

                    $baseExists = OrderBaseData::where("order_id", $orderId)->exists();
                    if( $baseExists === true ){
                        $updateResult = $this->orderW1->orderUpdate([$orderId]);
                        if( isset($updateResult["data"]["failList"]) && !empty($updateResult["data"]["failList"]) ){
                            $errArray = [
                                "msg"     => $updateResult["data"]["failList"][0]["msg"],
                                "message" => $message
                            ];
                            throw new ArrayValueError($errArray);
                        }

                        $messageCode = MessageConstant::MESSAGE_CODE[$type];

                        if( in_array($messageCode, [MessageConstant::OS001, MessageConstant::OS002, MessageConstant::OT002]) ){
                            $inBaseObj = BonaeraInBaseData::where("order_id", $orderId)->first();
                            switch ($messageCode) {
                                case MessageConstant::OT002:
                                    if( $inBaseObj !== null ){
                                        $bonaeraStockModifyApiDtos = $this->wmsW1->bonaeraStockModifyApiBindOT002($orderId, $logisticsId);
                                        /** 재고신청서 수정 */
                                        $this->wmsW1->bonaeraStockModifyApiBindCall($orderId, $bonaeraStockModifyApiDtos, $messageCode);
                                    }
                                    break;
                                case MessageConstant::OS001:
                                case MessageConstant::OS002:
                                    $inPrdObj = BonaeraInProductData::where("order_id", $orderId)->first();
                                    if( $inPrdObj === null ){
                                        /** 입고신청 */
                                        $this->wmsW1->bonaeraCreateStockApi($orderId);
                                    } else {
                                        $bonaeraStockModifyApiDtos = $this->wmsW1->bonaeraStockModifyApiBindOS002($orderId);
                                        /** 재고신청서 수정 */
                                        $this->wmsW1->bonaeraStockModifyApiBindCall($orderId, $bonaeraStockModifyApiDtos, $messageCode);
                                    }
                                    break;
                                default:
                                    break;
                            }

                        } else if( in_array($messageCode, [MessageConstant::OT001]) ){

                        }

                        debug_log(json_encode($logParams, JSON_UNESCAPED_UNICODE), "1688/message", "success-message");
                        $returnMsg = helpers_success_message();
                    }
                } else {
                    $errArray = [
                        "msg"     => MessageErrorMessageConstant::getNotHaveErrorMessage("TYPE"),
                        "message" => $message
                    ];
                    throw new ArrayValueError($errArray);
                }
            }
        } catch (ArrayValueError $e) {
            $errorArray                   = $e->getErrorArray();
            $params["message"]            = $errorArray["message"];
            $params["arrayValueErrorMsg"] = $errorArray["msg"];
            debug_log(json_encode($params, JSON_UNESCAPED_UNICODE), "1688/message", "error-message", LogLevel::ERROR);

            $returnMsg = helpers_fail_message($errorArray["msg"]);
        } catch (Exception $e) {
            $params = [
                "message"           => $message,
                "exceptionErrorMsg" => $e->getMessage(),
            ];
            debug_log(json_encode($params, JSON_UNESCAPED_UNICODE), "1688/message", "error-message", LogLevel::ERROR);

            $returnMsg = helpers_fail_message($e->getMessage());
        }

        if( $returnMsg["isSuccess"] === true ){
            $orderPubSubDtoBind = [
                'type'    => $messageCode,
                'orderId' => $orderId,
                'message' => $message
            ];
            $orderPubSubDto = new OrderPubSubDto();
            $orderPubSubDto->bind($orderPubSubDtoBind);
            event(new OrderPubSubEvent($orderPubSubDto));
        }

        return $returnMsg;
        
    }
}