<?php

namespace Tests\Feature;

use App\Constants\KafkaConstant;
use App\Constants\MallConstant;
use App\Constants\MessageConstant;
use App\Events\OrderPubSubEvent;
use App\Jobs\WmsJob;
use App\Packages\Kafka;
use App\Services\Wms\WmsService;
use App\Vo\Bonaera\BonaeraRequestQueueDto;
use App\Vo\Order\OrderPubSubDto;
use Carbon\Carbon;
use Tests\TestCase;

class KafkaTest extends TestCase
{
    # php artisan test --filter testKafkaProducer
    public function testKafkaProducer()
    {
        $topic = env("TEST_TOPIC", KafkaConstant::TEST);
        $test  = [
            "type"       => "PM001",
            "offer_id"   => 590177967435,
            "created_at" => Carbon::now()->format('Y-m-d H:i:s')
        ];
        $producer = new Kafka();
        $result   = $producer->sendQueue($topic, $test);

        $this->assertTrue($result);
    }

    # php artisan test --filter testKafkaConsumer
    public function testKafkaConsumer()
    {
        $producer = new Kafka();
        $producer->consume(KafkaConstant::TEST, MallConstant::MALL_ONCHANNEL);
    }

    # php artisan test --filter testWmsPubSubOrder
    public function testWmsPubSubOrder()
    {
        $orderPubSubDtoBind = [
            'type'    => MessageConstant::OS001,
            'orderId' => "2393111535014135493",
            'message' => []
        ];
        $orderPubSubDto = new OrderPubSubDto();
        $orderPubSubDto->bind($orderPubSubDtoBind);
        $result = event(new OrderPubSubEvent($orderPubSubDto));
        
        $this->assertNull($result[0]);
    }

    # php artisan test --filter testWmsOutKafkaConsumer
    public function testWmsOutKafkaConsumer()
    {
        $type                       = "GR002";
        $shNo                       = "SH241205004228";
        $originGroupNo              = "GR241205004227";
        $changeGroupNo              = "GR241205004227";
        $bonaeraRequestQueueDtoBind = [
            "type"          => $type,
            "shNo"          => $shNo,
            "originGroupNo" => $originGroupNo,
            "changeGroupNo" => $changeGroupNo,
        ];
        $bonaeraRequestQueueDto = new BonaeraRequestQueueDto();
        $bonaeraRequestQueueDto->bind($bonaeraRequestQueueDtoBind);

        $wmsJob       = new WmsJob($bonaeraRequestQueueDto);
        $wmsJob->handle(app(WmsService::class), app(Kafka::class));
    }
}
