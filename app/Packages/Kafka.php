<?php

namespace App\Packages;

use Junges\Kafka\Facades\Kafka as KafkaClient;
use Junges\Kafka\Message\Message;
use Throwable;
use Psr\Log\LogLevel;

class Kafka
{
    public function sendQueue(string $topic, array $message): bool
    {
        $result = true;

        try {
            KafkaClient::publishOn($topic)
                ->withMessage(
                    (new Message())
                    ->withBody($message)
                )
                ->send();
        } catch (Throwable $th) {
            $result  = false;
            $payload = [
                'topic'   => $topic,
                'message' => $message,
                'error'   => $th->getMessage(),
            ];
            debug_log(json_encode($payload, JSON_UNESCAPED_UNICODE), "kafka/sendQueue", "error-message", LogLevel::ERROR);
        }

        return $result;
    }

    public function consume(string $topic, string $group): void
    {
        try {
            KafkaClient::createConsumer()
                ->subscribe($topic)
                ->withConsumerGroupId($group)
                ->withAutoCommit()
                ->withHandler(function($message) use($group, $topic) {
                    $msg = "topic: {$topic} | part: {$message->getPartition()} | group: {$group} | message: {$message->getBody()}";
                    debug_log($msg, "kafka/consumer", "consumer");
                })
                ->build()
                ->consume();
                
        } catch (Throwable $th) {
            $msg = "error: " . $th->getMessage();
            debug_log($msg, "kafka/consume", "error-message", LogLevel::ERROR);
        }
    }
}
