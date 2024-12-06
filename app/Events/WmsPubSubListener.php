<?php

namespace App\Events;

use App\Constants\KafkaConstant;
use App\Constants\WmsConstant;
use App\Models\BonaeraOutBaseData;
use App\Packages\Kafka;
use App\Services\Wms\WmsW1;
use Psr\Log\LogLevel;

class WmsPubSubListener
{
    private WmsW1 $wmsw1;
    public Kafka $kafka;

    public function __construct(WmsW1 $wmsw1, Kafka $kafka) {
        $this->wmsw1 = $wmsw1;
        $this->kafka = $kafka;
    }

    public function handle(WmsPubSubEvent $event)
    {
        $type           = $event->wmsPubSubDto->type;
        $stockNo        = $event->wmsPubSubDto->stockNo;
        $groupNo        = $event->wmsPubSubDto->groupNo;
        $changeGroupNo  = $event->wmsPubSubDto->changeGroupNo;
        $orderId        = $event->wmsPubSubDto->orderId;
        $channelOrderId = $event->wmsPubSubDto->channelOrderId;
        
        # WMS 정보 Pub/Sub
        switch ($type) {
            case WmsConstant::WMS_CODE_TYPE_IT000:
            case WmsConstant::WMS_CODE_TYPE_IT001:
            case WmsConstant::WMS_CODE_TYPE_IT002:
                $kafkaPayload = $this->wmsw1->bindPubSubInData($type, $stockNo);
                if( !empty($kafkaPayload) ){
                    $isSuccess = $this->kafka->sendQueue(KafkaConstant::WAPP, $kafkaPayload);
                    if( $isSuccess !== true ) {
                        debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "error-pub&sub", LogLevel::ERROR);
                    } else {
                        debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "success-pub&sub");
                    }
                }
                break;
            case WmsConstant::WMS_CODE_TYPE_SH000:
                $kafkaPayload = $this->wmsw1->bindPubSubOutData(WmsConstant::WMS_CODE_TYPE_SH000, $orderId, $channelOrderId);
                if( !empty($kafkaPayload) ){
                    $isSuccess = $this->kafka->sendQueue(KafkaConstant::WAPP, $kafkaPayload);
                    if( $isSuccess !== true ) {
                        debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "error-pub&sub", LogLevel::ERROR);
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
                        $isSuccess = $this->kafka->sendQueue(KafkaConstant::WAPP, $kafkaPayload);
                        if( $isSuccess !== true ) {
                            debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "error-pub&sub", LogLevel::ERROR);
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
                        $isSuccess = $this->kafka->sendQueue(KafkaConstant::WAPP, $kafkaPayload);
                        if( $isSuccess !== true ) {
                            debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "error-pub&sub", LogLevel::ERROR);
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