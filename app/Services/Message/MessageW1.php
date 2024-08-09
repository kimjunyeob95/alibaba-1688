<?php

namespace App\Services\Message;

use App\Abstracts\OrderAbstract;
use App\Abstracts\WMessageAbstract;
use App\Constants\KafkaConstant;
use App\Constants\MessageConstant;
use App\Constants\MessageErrorMessageConstant;
use App\Exceptions\ArrayValueError;
use App\Models\OrderBaseData;
use App\Models\WMessageLog;
use App\Packages\Kafka;
use Carbon\Carbon;
use Exception;

class MessageW1 extends WMessageAbstract
{
    private OrderAbstract $orderW1;
    private Kafka $kafka;

    public function __construct(
        OrderAbstract $orderW1,
        Kafka $kafka
    )
    {
        parent::__construct();
        $this->orderW1 = $orderW1;
        $this->kafka   = $kafka;
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
                    }
                    
                    if( $orderId === "" ){
                        $errArray = [
                            "msg"     => MessageErrorMessageConstant::getNotHaveErrorMessage("ORDERID"),
                            "message" => $message
                        ];
                        throw new ArrayValueError($errArray);
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

                        $baseObj = OrderBaseData::with([
                            "logistics",
                            "w_options.option",
                            "channel_objs.details"
                        ])->where("order_id", $orderId)->first();

                        foreach ($baseObj->channel_objs as $channelObj) {    
                            $refund_info = [];
                            if( isset($message["data"]["refundAction"]) && isset($message["data"]["operator"]) ){
                                $refund_info = [
                                    "refund_action" => $message["data"]["refundAction"],
                                    "oprerator"     => $message["data"]["operator"],
                                ];
                            }
    
                            $options = [];
                            foreach ($channelObj->details as $optDetail) {
                                foreach ($baseObj->w_options as $wOption) {
                                    if( $optDetail->option_id == $wOption->option->id ){
                                        $options[] = [
                                            "option_id"        => $optDetail->option_id,
                                            "status"           => $wOption->status,
                                            "logistics_status" => $wOption->logistics_status,
                                            "refunds_stautus"  => $wOption->refund_status,
                                        ];
                                    }
                                }
                            }
    
                            $logisticsInfos = [];
                            foreach ($baseObj->logistics as $logistic) {
                                $logisticsInfos[] = [
                                    "logistics_code"         => $logistic->logistics_code,
                                    "logistics_company_name" => $logistic->logistics_company_name,
                                    "logistics_bill_no"      => $logistic->logistics_bill_no,
                                    "status"                 => $logistic->status
                                ];
                            }
    
                            $kafkaPayload = [
                                "type"             => MessageConstant::MESSAGE_CODE[$type],
                                "channel"          => $baseObj->channel,
                                "order_id"         => $baseObj->order_id,
                                "channel_order_id" => $channelObj->channel_order_id,
                                "offer_id"         => $baseObj->offer_id,
                                "order_data"       => [
                                    "status"        => $baseObj->status,
                                    "refund_status" => $baseObj->refund_status,
                                    "refund_info"   => $refund_info,
                                ],
                                "options"        => $options,
                                "logistics_info" => $logisticsInfos,
                                "created_at"     => Carbon::now(),
                            ];
                            
                            $pubSubSend = MessageConstant::PUB_SUB_SEND_Y;
                            $isSuccess  = $this->kafka->sendQueue(KafkaConstant::WAPP, json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE));
                            if( $isSuccess !== true ) {
                                debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "1688/message", "kafka-error-message");
                                $pubSubSend = MessageConstant::PUB_SUB_SEND_N;
                            }

                            WMessageLog::create([
                                "order_id"         => $baseObj->order_id,
                                "channel_order_id" => $channelObj->channel_order_id,
                                "code"             => MessageConstant::MESSAGE_CODE[$type],
                                "request"          => json_encode($logParams, JSON_UNESCAPED_UNICODE),
                                "pub_sub_msg"      => json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE),
                                "pub_sub_is_send"  => $pubSubSend,
                            ]);
                            debug_log(json_encode($logParams, JSON_UNESCAPED_UNICODE), "1688/message", "success-message");
                        }
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
            $errorArray          = $e->getErrorArray();
            $params["message"]   = $errorArray["message"];
            $params["error_msg"] = $errorArray["msg"];
            debug_log(json_encode($params, JSON_UNESCAPED_UNICODE), "1688/message", "error-message");
        } catch (Exception $e) {
            debug_log($e->getMessage(), "1688/message", "error-message");
        }

        /** 200으로 반환 안할 시 1688에서 재전송함 */
        $returnMsg = helpers_success_message();

        return $returnMsg;
        
    }
}