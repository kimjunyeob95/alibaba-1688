<?php

namespace App\Jobs;

use App\Constants\WmsConstant;
use App\Models\BonaeraInBaseData;
use App\Models\BonaeraOutBaseData;
use App\Services\Wms\WmsService;
use App\Vo\Bonaera\BonaeraRequestQueueDto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public BonaeraRequestQueueDto $bonaeraRequestQueueDto;

    public function __construct(BonaeraRequestQueueDto $bonaeraRequestQueueDto)
    {
        $this->bonaeraRequestQueueDto = $bonaeraRequestQueueDto;
    }

    public function handle(WmsService $wmsService): void
    {   
        switch ($this->bonaeraRequestQueueDto->type) {
            case WmsConstant::WMS_CODE_TYPE_IT001:
            case WmsConstant::WMS_CODE_TYPE_IT002:
                $obj = BonaeraInBaseData::where("stock_no", $this->bonaeraRequestQueueDto->stockNo)->first();
                if( $obj !== null ){
                    $wmsService->bonaeraInUpdate($obj->id);
                }
                break;
            case WmsConstant::WMS_CODE_TYPE_SH001:
            case WmsConstant::WMS_CODE_TYPE_SH002:
                $objs = BonaeraOutBaseData::where("group_no", $this->bonaeraRequestQueueDto->groupNo)->get();
                foreach ($objs as $obj) {
                    $wmsService->bonaeraOutUpdate($obj->id);
                }
                break;
            default:
                break;
        }

        $payload = $this->bonaeraRequestQueueDto->getAllProperties();
        debug_log(json_encode($payload, JSON_UNESCAPED_UNICODE), "boneara/request", "bonaeraRequest");
    }
}
