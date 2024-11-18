<?php

namespace App\Packages;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Throwable;

class Slack
{
    private array $returnMsg;
    private Client $client;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
        $this->client    = new Client();
    }

    public function sendMessage(string $webhookUrl, string $message): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $response = $this->client->post($webhookUrl, [
                'json' => [
                    'text' => $message
                ]
            ]);

            if ($response->getStatusCode() === 200) {
                $returnMsg = helpers_success_message();
            } else {
                $body         = json_decode($response->getBody()->getContents(), true);
                $errorMessage = $body['error'] ?? '알 수 없는 에러가 발생했습니다';
                throw new GuzzleException($errorMessage);
            }
        } catch (GuzzleException $e) {
            $returnMsg = helpers_fail_message('Slack error: ' . $e->getMessage());
        } catch (Throwable $e) {
            $returnMsg = helpers_fail_message('Throwable error: ' . $e->getMessage());
        }
        return $returnMsg;
    }
    
}
