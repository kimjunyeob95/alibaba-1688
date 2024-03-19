<?php

namespace App\Vo\Genuio;

use App\Vo\Vo;

class QueueDto extends Vo
{
    protected int $offer_id         = 0;
    protected string $payload_json  = "";
    protected string $request_user  = "";
    protected string $response_json = "";

    public function bind(mixed $data): void
    {
        $this->offer_id      = $data["offerId"];
        $this->payload_json  = $data["payload_json"];
        $this->request_user  = $data["request_user"];
        $this->response_json = $data["response_json"];
    }
}