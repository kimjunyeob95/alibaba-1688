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
            case WmsConstant::WMS_CODE_TYPE_GR001:
            case WmsConstant::WMS_CODE_TYPE_GR002:
                $objs = BonaeraOutBaseData::where("group_no", $this->bonaeraRequestQueueDto->groupNo)->get();
                foreach ($objs as $obj) {
                    $wmsService->bonaeraOutUpdate($obj->id);
                }
                break;
            case WmsConstant::WMS_CODE_TYPE_GR003:
                $obj = BonaeraOutBaseData::where([
                    "sh_no"    => $this->bonaeraRequestQueueDto->shNo,
                    "group_no" => $this->bonaeraRequestQueueDto->originGroupNo
                ])->first();
                if( $obj !== null ){
                    $wmsService->bonaeraDeliveryBundle($obj->id, $this->bonaeraRequestQueueDto->changeGroupNo);
                }
                break;
            default:
                break;
        }

        $getAllProperties = $this->bonaeraRequestQueueDto->getAllProperties();
        $payload          = [];
        foreach ($getAllProperties as $key => $property) {
            if( !empty($property) ){
                $camelKey           = snakeToCamelCase($key);
                $payload[$camelKey] = $property;
            }
        }

        debug_log(json_encode($payload, JSON_UNESCAPED_UNICODE), "boneara/request", "bonaeraRequest");
    }
}
