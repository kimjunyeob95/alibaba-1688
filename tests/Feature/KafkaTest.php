<?php

namespace Tests\Feature;

use App\Constants\KafkaConstant;
use App\Constants\MallConstant;
use App\Packages\Kafka;
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
        $result   = $producer->produce(KafkaConstant::WAPP, json_encode($test));

        $this->assertTrue($result);
    }

    # php artisan test --filter testKafkaHardCodeProducer
    public function testKafkaHardCodeProducer()
    {
        $test = [
            "type"       => "PM001",
            "offer_id"   => 590177967435,
            "created_at" => Carbon::now()->format('Y-m-d H:i:s')
        ];
        $producer = new Kafka();
        $result   = $producer->produce2("test", json_encode($test));

        $this->assertTrue($result);
    }

    # php artisan test --filter testKafkaConsumer
    public function testKafkaConsumer()
    {
        $producer = new Kafka();
        $producer->consume(KafkaConstant::WAPP, MallConstant::MALL_ONCHANNEL);
    }

}
