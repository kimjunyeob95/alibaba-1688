<?php

namespace App\Packages;

use Kafka\Producer;
use Kafka\Consumer;
use Kafka\ConsumerConfig;
use Kafka\ProducerConfig;

class Kafka
{
    private string $brokers;

    public function __construct()
    {
        $this->brokers = env('KAFKA_BROKERS', '115.68.48.70:9091,115.68.48.70:9092,115.68.48.70:9093');
    }

    public function produce(string $topic, string $message): bool
    {
        $config = ProducerConfig::getInstance();
        $config->setMetadataBrokerList($this->brokers);
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
        $producer->error(function($errorCode) use (&$isSuccess) {
            $isSuccess = false;
        });

        $producer->send(true);

        return $isSuccess;
    }

    public function consume(string $topic, string $group): void
    {
        $config = ConsumerConfig::getInstance();
        $config->setMetadataBrokerList($this->brokers);
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
