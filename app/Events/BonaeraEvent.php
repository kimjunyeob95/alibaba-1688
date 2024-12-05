<?php

namespace App\Events;

use App\Vo\Bonaera\BonaeraEventDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BonaeraEvent
{
    use Dispatchable, SerializesModels;

    public BonaeraEventDto $bonaeraEventDto;

    public function __construct(BonaeraEventDto $bonaeraEventDto)
    {
        $this->bonaeraEventDto = $bonaeraEventDto;
    }
}