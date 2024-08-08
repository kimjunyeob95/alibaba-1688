<?php

namespace App\Packages;

use Kafka\Producer;
use Kafka\Consumer;
use Kafka\ConsumerConfig;
use Kafka\ProducerConfig;

class Kafka
{
    protected string $brokers;
    protected Producer $producer;

    public function __construct()
    {
        $this->brokers = env('KAFKA_BROKERS', 'sellerhub-broker01:9092,sellerhub-broker02:9092,sellerhub-broker03:9092');

        $config = ProducerConfig::getInstance();
        $config->setMetadataBrokerList($this->brokers);
        $config->setMetadataRefreshIntervalMs(10000);
        $config->setBrokerVersion('2.0.0');
        $config->setRequiredAck(1);
        $config->setIsAsyn(false);
        $config->setProduceInterval(500);

        $this->producer = new Producer();
    }

    public function sendQueue(string $topic, string $message): bool
    {
        $result = $this->producer->send([
            [
                'topic' => $topic,
                'value' => $message
            ],
        ]);

        if( empty($result) ){
            return false;
        }

        return true;
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
