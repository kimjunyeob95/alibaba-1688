<?php

namespace App\Packages;

use Kafka\Producer;
use Kafka\Consumer;
use Kafka\ConsumerConfig;
use Kafka\ProducerConfig;
use Psr\Log\LogLevel;
use Throwable;

class Kafka
{
    protected string $brokers;
    protected Producer $producer;

    public function __construct()
    {
        try {
            $this->brokers = env('KAFKA_BROKERS', 'sellerhub-broker01:9092,sellerhub-broker02:9092,sellerhub-broker03:9092');
    
            $config = ProducerConfig::getInstance();
            $config->setMetadataBrokerList($this->brokers);
            $config->setMetadataRefreshIntervalMs(10000);
            $config->setBrokerVersion('2.0.0');
            $config->setRequiredAck(1);
            $config->setIsAsyn(false);
            $config->setProduceInterval(500);
    
            $this->producer = new Producer();
        } catch (Throwable $th) {
            $this->producer = null;
        }
    }

    public function sendQueue(string $topic, string $message): bool
    {
        if( $this->isHealthy() === false ){
            $msg = "error: Kafka health check failed";
            debug_log($msg, "kafka", "error-message", LogLevel::ERROR);

            return false;
        } else {
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

    }

    public function isHealthy(): bool
    {
        try {
            // Producer가 초기화되었는지 확인
            if ($this->producer === null) {
                return false;
            }

            // ProducerConfig가 정상적으로 설정되었는지 확인
            $config  = ProducerConfig::getInstance();
            $brokers = $config->getMetadataBrokerList();
            
            return !empty($brokers);

        } catch (Throwable $th) {
            return false;
        }
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
