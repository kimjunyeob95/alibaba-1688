<?php

namespace App\Vo\Genuio;

use App\Constants\GenuioConstant;
use App\Vo\Vo;

class QueueDto extends Vo
{
    protected int $offer_id         = 0;
    protected int $parent_id        = 0;
    protected int $send_type        = GenuioConstant::IMG_TRANS;
    protected string $payload_json  = "";
    protected string $request_user  = "";
    protected string $response_json = "";

    public function bind(mixed $data): void
    {
        $this->offer_id      = $data["offerId"];
        $this->parent_id     = $data["parent_id"];
        $this->send_type     = $data["send_type"];
        $this->payload_json  = $data["payload_json"];
        $this->request_user  = $data["request_user"];
        $this->response_json = $data["response_json"];
    }
}