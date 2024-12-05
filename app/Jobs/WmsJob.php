<?php

namespace App\Jobs;

use App\Constants\KafkaConstant;
use App\Constants\MallConstant;
use App\Constants\WmsConstant;
use App\Models\BonaeraInBaseData;
use App\Models\BonaeraOutBaseData;
use App\Packages\Kafka;
use App\Services\Wms\WmsService;
use App\Vo\Bonaera\BonaeraRequestQueueDto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class WmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public BonaeraRequestQueueDto $bonaeraRequestQueueDto;

    public function __construct(BonaeraRequestQueueDto $bonaeraRequestQueueDto)
    {
        $this->bonaeraRequestQueueDto = $bonaeraRequestQueueDto;
    }

    public function handle(WmsService $wmsService, Kafka $kafka): void
    {   
        switch ($this->bonaeraRequestQueueDto->type) {
            case WmsConstant::WMS_CODE_TYPE_IT001:
            case WmsConstant::WMS_CODE_TYPE_IT002:
                $obj = BonaeraInBaseData::where("stock_no", $this->bonaeraRequestQueueDto->stockNo)->first();
                if( $obj !== null ){
                    $wmsService->bonaeraInUpdate($obj->id);

                    $kafkaPayload = $this->bindInData($this->bonaeraRequestQueueDto->type, $this->bonaeraRequestQueueDto->stockNo);
                    $isSuccess    = $kafka->sendQueue(KafkaConstant::WAPP, json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE));
                    if( $isSuccess !== true ) {
                        debug_log(json_encode($kafkaPayload, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "error-kafka");
                    }
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

    public function bindInData(string $type, string $stockNo): array
    {
        $payload = [];

        try {
            $inObj = BonaeraInBaseData::with(["order.channel_obj", "in_options.imgs"])
            ->where("stock_no", $stockNo)->first();
            if( $inObj !== null ){
                $itemInfos = [];
                foreach ($inObj->in_options as $inOption) {
                    $images = [];
                    foreach ($inOption->imgs as $img) {
                        $images[] = [
                            'img_number' => $img->img_number,
                            'img_url'    => $img->img_url,
                        ];
                    }

                    $itemInfo = [
                        'option_id'     => $inOption->option_id,
                        'it_code'       => $inOption->it_code,
                        'quantity'      => $inOption->quantity,
                        'status'        => $inOption->status,
                        'in_comming_at' => $inOption->in_comming_at,
                        'memo'          => $inOption->memo,
                        'images'        => $images,
                    ];

                    $itemInfos[] = $itemInfo;
                }

                $payload = [
                    'type'             => $type,
                    'channel'          => MallConstant::MALL_ONCHANNEL,
                    'order_id'         => $inObj->order->order_id,
                    'channel_order_id' => $inObj->order->channel_obj->channel_order_id,
                    'offer_id'         => $inObj->order->offer_id,
                    'in_data'          => [
                        'stock_no'   => $stockNo,
                        'item_infos' => $itemInfos
                    ],
                ];
            }
        } catch (Throwable $th) {
            $errorMsg = [
                "type"     => $type,
                "stock_no" => $stockNo,
                "error"    => $th->getMessage(),
            ];
            debug_log(json_encode($errorMsg, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "error-bindInData");
        }

        return $payload;
    }
}
