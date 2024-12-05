<?php

namespace App\Events;

use App\Vo\Wms\WmsPubSubDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WmsPubSubEvent
{
    use Dispatchable, SerializesModels;

    public WmsPubSubDto $wmsPubSubDto;

    public function __construct(WmsPubSubDto $wmsPubSubDto)
    {
        $this->wmsPubSubDto = $wmsPubSubDto;
    }
}