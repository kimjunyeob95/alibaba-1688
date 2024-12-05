<?php

namespace App\Events;

use App\Constants\KafkaConstant;
use App\Constants\MessageConstant;
use App\Models\OrderBaseData;
use App\Packages\Kafka;
use App\Services\Wms\WmsW1;

class OrderPubSubListener
{
    private WmsW1 $wmsw1;
    private Kafka $kafka;

    public function __construct(WmsW1 $wmsw1, Kafka $kafka) {
        $this->wmsw1 = $wmsw1;
        $this->kafka = $kafka;
    }

    public function handle(OrderPubSubEvent $event)
    {
        $type    = $event->orderPubSubDto->type;
        $orderId = $event->orderPubSubDto->orderId;
        $message = $event->orderPubSubDto->message;

        # 주문 정보 Pub/Sub 등록
        if( in_array($type, MessageConstant::MESSAGE_CODE) ){
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
        }       
    }
}