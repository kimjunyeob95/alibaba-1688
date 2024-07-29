<?php

namespace App\Packages;

use Kafka\Consumer;
use Kafka\ConsumerConfig;

class KafkaConsumer
{
    public function consume(string $topic, string $group)
    {
        $config = ConsumerConfig::getInstance();
        $config->setMetadataBrokerList(env('KAFKA_BROKERS', '115.68.48.70:9091,115.68.48.70:9092,115.68.48.70:9093'));
        $config->setGroupId($group);
        $config->setTopics([$topic]);
        $config->setOffsetReset('earliest');
        $config->setBrokerVersion('2.0.0');

        $consumer = new Consumer();
        $consumer->start(function($topic, $part, $message) use($group) {
            $msg = "topic: {$topic} | part: {$part} | group: {$group} | message: " . $message['message']['value'];
            debug_log($msg, "kafka/consumer", "consumer");
        });
    }
}
