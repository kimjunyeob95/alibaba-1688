<?php

namespace App\Events;

use App\Constants\KafkaConstant;
use App\Constants\MessageConstant;
use App\Constants\WmsConstant;
use App\Models\BonaeraOutBaseData;
use App\Models\OrderBaseData;
use App\Packages\Kafka;
use App\Services\Wms\WmsW1;

class BonaeraListener
{
    private WmsW1 $wmsw1;
    private Kafka $kafka;

    public function __construct(WmsW1 $wmsw1, Kafka $kafka) {
        $this->wmsw1 = $wmsw1;
        $this->kafka = $kafka;
    }

    public function handle(BonaeraEvent $event)
    {
        $type           = $event->bonaeraEventDto->type;
        $stockNo        = $event->bonaeraEventDto->stockNo;
        $groupNo        = $event->bonaeraEventDto->groupNo;
        $changeGroupNo  = $event->bonaeraEventDto->changeGroupNo;
        $orderId        = $event->bonaeraEventDto->orderId;
        $channelOrderId = $event->bonaeraEventDto->channelOrderId;
        $message        = $event->bonaeraEventDto->message;

        if( in_array($type, MessageConstant::MESSAGE_CODE) ){
            # 주문 정보 Pub/Sub

            $baseObj = OrderBaseData::with([
                "logistics",
                "w_options.option",
                "channel_objs.details.option"
            ])->where("order_id", $orderId)->first();

            foreach ($baseObj->channel_objs as $channelObj) {
                $kafkaPayload = $this->wmsw1->bindPubSubOrderData($baseObj, $channelObj, $type, $message);
                if( !empty($kafkaPayload) ){
                    $isSuccess = $this->kafka->sendQueue(KafkaConstant::WAPP, json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE));
                    if( $isSuccess !== true ) {
                        debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "error-pub&sub");
                    } else {
                        debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "success-pub&sub");
                    }
                }
            }

        } else {
            # WMS 정보 Pub/Sub

            switch ($type) {
                case WmsConstant::WMS_CODE_TYPE_IT000:
                case WmsConstant::WMS_CODE_TYPE_IT001:
                case WmsConstant::WMS_CODE_TYPE_IT002:
                    $kafkaPayload = $this->wmsw1->bindPubSubInData($type, $stockNo);
                    if( !empty($kafkaPayload) ){
                        $isSuccess = $this->kafka->sendQueue(KafkaConstant::WAPP, json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE));
                        if( $isSuccess !== true ) {
                            debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "error-pub&sub");
                        } else {
                            debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "success-pub&sub");
                        }
                    }
                    break;
                case WmsConstant::WMS_CODE_TYPE_SH000:
                    $kafkaPayload = $this->wmsw1->bindPubSubOutData(WmsConstant::WMS_CODE_TYPE_SH000, $orderId, $channelOrderId);
                    if( !empty($kafkaPayload) ){
                        $isSuccess = $this->kafka->sendQueue(KafkaConstant::WAPP, json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE));
                        if( $isSuccess !== true ) {
                            debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "error-pub&sub");
                        } else {
                            debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "success-pub&sub");
                        }
                    }
                    break;
                case WmsConstant::WMS_CODE_TYPE_SH001:
                case WmsConstant::WMS_CODE_TYPE_SH002:
                case WmsConstant::WMS_CODE_TYPE_GR001:
                case WmsConstant::WMS_CODE_TYPE_GR002:
                    $outObjs = BonaeraOutBaseData::where("group_no", $groupNo)
                    ->groupBy("channel_order_id")->get();
    
                    foreach ($outObjs as $outObj) {
                        $kafkaPayload = $this->wmsw1->bindPubSubOutData($type, $outObj->order_id, $outObj->channel_order_id);
                        if( !empty($kafkaPayload) ){
                            $isSuccess = $this->kafka->sendQueue(KafkaConstant::WAPP, json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE));
                            if( $isSuccess !== true ) {
                                debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "error-pub&sub");
                            } else {
                                debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "success-pub&sub");
                            }
                        }
                    }
                    break;
                case WmsConstant::WMS_CODE_TYPE_GR003:
                    $outObjs = BonaeraOutBaseData::where("group_no", $changeGroupNo)
                    ->groupBy("channel_order_id")->get();
                    foreach ($outObjs as $outObj) {
                        $kafkaPayload = $this->wmsw1->bindPubSubOutData($type, $outObj->order_id, $outObj->channel_order_id);
                        if( !empty($kafkaPayload) ){
                            $isSuccess = $this->kafka->sendQueue(KafkaConstant::WAPP, json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE));
                            if( $isSuccess !== true ) {
                                debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "error-pub&sub");
                            } else {
                                debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "success-pub&sub");
                            }
                        }
                    }
                    break;
                default:
                    break;
            }
        }       
    }
}