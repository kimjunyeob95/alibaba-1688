<?php

namespace App\Packages;

use Kafka\Producer;
use Kafka\ProducerConfig;

class KafkaProducer
{
    public function produce(string $topic, string $message): bool
    {
        $config = ProducerConfig::getInstance();
        $config->setMetadataBrokerList(env('KAFKA_BROKERS', '115.68.48.70:9091,115.68.48.70:9092,115.68.48.70:9093'));
        $config->setBrokerVersion('2.0.0');

        $producer = new Producer(function() use ($message, $topic) {
            return [
                [
                    'topic' => $topic,
                    'value' => $message,
                    'key'   => null, // 라운도로빈
                ],
            ];
        });

        $isSuccess = false;
        $producer->success(function() use (&$isSuccess) {
            $isSuccess = true;
        });
        $producer->error(function() use (&$isSuccess) {
            $isSuccess = false;
        });

        $producer->send(true);

        return $isSuccess;
    }
}
