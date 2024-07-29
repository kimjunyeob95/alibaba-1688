<?php

namespace Tests\Feature;

use App\Constants\KafkaConstant;
use App\Constants\MallConstant;
use App\Packages\Kafka;
use Tests\TestCase;

class KafkaTest extends TestCase
{
    # php artisan test --filter testKafkaProducer
    public function testKafkaProducer()
    {
        $test = [
            "type"     => "PM001",
            "offer_id" => 590177967435,
        ];
        $producer = new Kafka();
        $result   = $producer->produce(KafkaConstant::WAPP, json_encode($test));
        dd($result);
    }

    # php artisan test --filter testKafkaConsumer
    public function testKafkaConsumer()
    {
        $producer = new Kafka();
        $producer->consume(KafkaConstant::WAPP, MallConstant::MALL_ONCHANNEL);
    }

}
