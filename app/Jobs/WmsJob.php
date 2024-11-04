<?php

namespace App\Jobs;

use App\Services\Message\WMessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function handle(WMessageService $wMessageService): void
    {   
        $wMessageService->message($this->params);
    }
}
