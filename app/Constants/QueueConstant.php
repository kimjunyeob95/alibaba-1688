<?php

namespace App\Constants;


class QueueConstant
{
    public const QUEUE_W_MESSAGE = "w_message";

    public const QUEUE_NAME = [
        self::QUEUE_W_MESSAGE => "w 주문 콜백",
    ];
    
}
