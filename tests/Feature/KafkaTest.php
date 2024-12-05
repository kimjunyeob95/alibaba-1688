<?php

namespace Tests\Feature;

use App\Constants\KafkaConstant;
use App\Constants\MallConstant;
use App\Jobs\WmsJob;
use App\Packages\Kafka;
use App\Services\Wms\WmsService;
use App\Vo\Bonaera\BonaeraRequestQueueDto;
use Carbon\Carbon;
use Tests\TestCase;

class KafkaTest extends TestCase
{
    # php artisan test --filter testKafkaProducer
    public function testKafkaProducer()
    {
        $test = [
            "type"       => "PM001",
            "offer_id"   => 590177967435,
            "created_at" => Carbon::now()->format('Y-m-d H:i:s')
        ];
        $producer = new Kafka();
        $result   = $producer->sendQueue(KafkaConstant::WAPP, json_encode($test));

        $this->assertTrue($result);
    }

    # php artisan test --filter testKafkaConsumer
    public function testKafkaConsumer()
    {
        $producer = new Kafka();
        $producer->consume(KafkaConstant::WAPP, MallConstant::MALL_ONCHANNEL);
    }

    # php artisan test --filter testWmsInKafkaConsumer
    public function testWmsInKafkaConsumer()
    {
        $type                       = "IT001";
        $stockNo                    = "ST241125003127";
        $bonaeraRequestQueueDtoBind = [
            "type"    => $type,
            "stockNo" => $stockNo,
        ];
        $bonaeraRequestQueueDto = new BonaeraRequestQueueDto();
        $bonaeraRequestQueueDto->bind($bonaeraRequestQueueDtoBind);

        $wmsJob       = new WmsJob($bonaeraRequestQueueDto);
        $wmsJob->handle(app(WmsService::class), app(Kafka::class));

        
    }

}
