<?php

namespace App\Abstracts;

use App\Constants\MallConstant;
use App\Constants\WmsConstant;
use App\Events\WmsPubSubEvent;
use App\Http\Request\Bonaera\BonaeraOutDeliveryUpdateRequest;
use App\Models\ApiUser;
use App\Models\BonaeraInBaseData;
use App\Models\BonaeraOutBaseData;
use App\Models\OrderBaseData;
use App\Models\OrderChannelData;
use App\Models\OrderLogisticsData;
use App\Packages\Bonaera;
use App\Packages\JwtPackage;
use App\Packages\Slack;
use App\Vo\Wms\WmsPubSubDto;
use Carbon\Carbon;
use Exception;
use Throwable;

abstract class WmsAbstract
{
    protected array $returnMsg;
    protected string $unipass_token;
    protected Bonaera $bonaera;
    private JwtPackage $jwtPackage;
    protected Slack $slack;

    public function __construct(Bonaera $bonaera, JwtPackage $jwtPackage, Slack $slack)
    {
        $this->returnMsg     = helpers_fail_message();
        $this->bonaera       = $bonaera;
        $this->jwtPackage    = $jwtPackage;
        $this->slack         = $slack;
        $this->unipass_token = env("UNIPASS_TOKEN", "g230z224g099q163r080k040u0");
    }

    /**
    * @func hsCodeList
    * @description 'HS code 라스트'
    * @param array $params
    * @return array
    */
    abstract function hsCodeList(array $params): array;

    /**
    * @func inList
    * @description '입고관리 라스트'
    * @param array $params
    * @return array
    */
    abstract function inList(array $params): array;

    /**
    * @func inFailList
    * @description '입고 실패 리스트'
    * @param array $params
    * @return array
    */
    abstract function inFailList(array $params): array;

    /**
    * @func apiHsCodeList
    * @description 'HS code 라스트'
    * @param array $params
    * @return array
    */
    public function apiHsCodeList(array $params): array
    {
        $result    = $this->hsCodeList($params);
        $paginator = $result["data"];
        $datas     = [];
        foreach ($paginator as $prdObj) {
           $datas[] = $prdObj;
        }
  
        $lastPage  = $paginator->lastPage();
        $page      = $paginator->currentPage();
        $pageSize  = $paginator->perPage();
  
        return [
           "result"    => $datas,
           "last_page" => $lastPage,
           "page"      => $page,
           "page_size" => (int)$pageSize,
        ];
    }

    /**
    * @func getTariff
    * @description '관세율조회'
    * @param string $hsCode
    * @return array
    */
    abstract function getTariff(string $hsCode): array;

    /**
    * @func bonaeraInHscodeUpdate
    * @description '입고성공 HS code 적용'
    * @param array $ids
    * @param string $hsCode
    * @return array
    */
    abstract function bonaeraInHscodeUpdate(array $ids, string $hsCode): array;

    /**
    * @func bonaeraInFailHscodeUpdate
    * @description '입고실패 HS code 적용'
    * @param array $ids
    * @param string $hsCode
    * @return array
    */
    abstract function bonaeraInFailHscodeUpdate(array $ids, string $hsCode): array;

    /**
    * @func bonaeraInFailCreate
    * @description '입고실패 데이터 입고신청'
    * @param int $id
    * @return void
    */
    abstract function bonaeraInFailCreate(int $id): void;

    /**
    * @func bonaeraCreateStockApi
    * @description '입고신청'
    * @param string $orderId
    * @return void
    */
    public function bonaeraCreateStockApi(string $orderId): void
    {
        try {
            $result = $this->bonaera->createStockApi($orderId);

            if( $result["isSuccess"] === true ){
                $wmsPubSubDtoBind = [
                    'type'    => WmsConstant::WMS_CODE_TYPE_IT000,
                    'stockNo' => $result["data"]["stock_no"],
                ];
                $wmsPubSubDto = new WmsPubSubDto();
                $wmsPubSubDto->bind($wmsPubSubDtoBind);
                event(new WmsPubSubEvent($wmsPubSubDto));
            }
        } catch (Throwable $e) {
            $msg = "error: " . $e->getMessage(). " | orderId: " . $orderId;
            debug_log($msg, "boneara/bonaeraCreateStockApi", "bonaeraCreateStockApi");
        }
    }

    /**
    * @func bonaeraInUpdate
    * @description '입고정보 업데이트'
    * @param int $id
    * @return void
    */
    abstract function bonaeraInUpdate(int $id): void;

    /**
    * @func bonaeraOutCreate
    * @description '출고신청'
    * @param int $id
    * @return void
    */
    abstract function bonaeraOutCreate(int $id): void;

    /**
    * @func bonaeraOutUpdate
    * @description '출고정보 업데이트'
    * @param int $id
    * @return void
    */
    abstract function bonaeraOutUpdate(int $id): void;

    /**
    * @func bonaeraOutPay
    * @description '출고 배송비 결제'
    * @param int $id
    * @return void
    */
    abstract function bonaeraOutPay(int $id): void;

    /**
    * @func bonaeraOutDeliveryUpdate
    * @description '출고 배송정보 업데이트'
    * @param BonaeraOutDeliveryUpdateRequest $request
    * @return array
    */
    abstract function bonaeraOutDeliveryUpdate(BonaeraOutDeliveryUpdateRequest $request): array;

    /**
    * @func inDetail
    * @description '입고정보 상세'
    * @param string $stockNo
    * @return array
    */
    abstract function inDetail(string $stockNo): array;

    /**
    * @func outSignList
    * @description '출고 신청관리'
    * @param array $params
    * @return array
    */
    abstract function outSignList(array $params): array;

    /**
    * @func outList
    * @description '출고관리 라스트'
    * @param array $params
    * @return array
    */
    abstract function outList(array $params): array;

    /**
    * @func outDetail
    * @description '출고정보 상세'
    * @param string $groupNo
    * @return array
    */
    abstract function outDetail(string $groupNo): array;

    /**
     * @func RequestBonaeraTokenCreate
     * @description '토큰 생성'
     * @param string $userId
     * @return array
    */
    public function RequestBonaeraTokenCreate(string $userId): array
    {
        $returnMsg = $this->returnMsg;
        try {            
            $result = $this->jwtPackage->tokenCreate($userId, WmsConstant::COMPANY_BONAERA);
            if( $result["isSuccess"] && isset($result["data"]["token"]) ){
                ApiUser::where([
                    'user_id'      => $userId,
                    'user_company' => WmsConstant::COMPANY_BONAERA,
                ])->update([
                    "updated_at" => Carbon::now()
                ]);
                $tokenResult = $result["data"];
                $returnMsg   = helpers_success_message($tokenResult);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
    * @func bonaeraDeliveryBundle
    * @description '묶음 배송 처리'
    * @param int $id
    * @param string $changeGroupNo
    * @return void
    */
    abstract function bonaeraDeliveryBundle(int $id, string $changeGroupNo): void;

    /**
    * @func bonaeraStockModifyApiBindCall
    * @description '재고신청서 수정 처리'
    * @param string $orderId
    * @param array $bonaeraStockModifyApiDtos
    * @param string $code
    * @return array
    */
    public function bonaeraStockModifyApiBindCall(string $orderId, array $bonaeraStockModifyApiDtos, string $code = ""): array
    {
        return $this->bonaera->stockModifyApiBindCall($orderId, $bonaeraStockModifyApiDtos, $code);
    }

    /**
    * @func bonaeraStockModifyApiBindOT002
    * @description '재고신청서 수정(OT002)'
    * @param string $orderId
    * @param string $logisticsCode
    * @return array
    */
    public function bonaeraStockModifyApiBindOT002(string $orderId, string $logisticsCode): array
    {
        return $this->bonaera->stockModifyApiBindOT002($orderId, $logisticsCode);
    }

    /**
    * @func bonaeraStockModifyApiBindOS002
    * @description '재고신청서 수정(OS002)'
    * @param string $orderId
    * @return array
    */
    public function bonaeraStockModifyApiBindOS002(string $orderId): array
    {
        return $this->bonaera->stockModifyApiBindOS002($orderId);
    }

    /**
    * @func bonaeraOutBox
    * @description '보내라 박스 정보'
    * @param string $groupNo
    * @return array
    */
    abstract function bonaeraOutBox(string $groupNo): array;

    /**
     * @func bindPubSubOrderData
     * @description '주문정보 pub/sub 메세지'
     * @param OrderBaseData $baseObj
     * @param OrderChannelData $channelObj
     * @param array $type
     * @param array $message
     * @return array
     */
    public function bindPubSubOrderData(OrderBaseData $baseObj, OrderChannelData $channelObj, string $type, array $message): array
    {
        $payload = [];

        try {
            $orderId = $baseObj->order_id;

            $statusChanged = "";
            if( isset($message["data"]["OrderLogisticsTracingModel"]["statusChanged"]) && $message["data"]["OrderLogisticsTracingModel"]["statusChanged"] ) {
                $statusChanged = $message["data"]["OrderLogisticsTracingModel"]["statusChanged"];
            }

            $refundInfo = [];
            if( isset($message["data"]["refundAction"]) && isset($message["data"]["operator"]) ){
                $refundInfo = [
                    "refund_action" => $message["data"]["refundAction"],
                    "operator"      => $message["data"]["operator"],
                ];
            }

            $options = [];
            foreach ($channelObj->details as $optDetail) {
                foreach ($baseObj->w_options as $wOption) {
                    if( $optDetail->option_id == $wOption->option->id ){
                        $logicObj = OrderLogisticsData::where("order_id", $orderId)
                        ->where('sub_item_ids', 'LIKE', '%' . $wOption->option->sub_item_id . '%')
                        ->first();
                        
                        $options[] = [
                            "option_id"        => $optDetail->option_id,
                            "status"           => $wOption->status,
                            "logistics_status" => $wOption->logistics_status,
                            "refund_status"    => $wOption->refund_status,
                            "logistics_code"   => $logicObj->logistics_code ?? ""
                        ];
                    }
                }
            }

            $logisticsInfos = [];
            foreach ($baseObj->logistics as $logistic) {
                $logisticsInfos[] = [
                    "logistics_code"         => $logistic->logistics_code,
                    "logistics_company_name" => $logistic->logistics_company_name,
                    "logistics_bill_no"      => $logistic->logistics_bill_no,
                    "status"                 => $logistic->status,
                    "status_changed"         => $statusChanged,
                ];
            }

            $payload = [
                "type"             => $type,
                "channel"          => $baseObj->channel,
                "order_id"         => $baseObj->order_id,
                "channel_order_id" => $channelObj->channel_order_id,
                "offer_id"         => $baseObj->offer_id,
                "order_data"       => [
                    "status"        => $baseObj->status,
                    "refund_status" => $baseObj->refund_status,
                    "refund_info"   => $refundInfo,
                ],
                "options"        => $options,
                "logistics_info" => $logisticsInfos,
                "created_at"     => Carbon::now(),
            ];
        } catch (Throwable $th) {
            $errorMsg = [
                "type"    => $type,
                "orderId" => $orderId,
                "error"   => $th->getMessage(),
            ];
            debug_log(json_encode($errorMsg, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "error-bindPubSubOrderData");
        }

        return $payload;
    }

    /**
     * @func bindPubSubInData
     * @description '입고정보 pub/sub 메세지'
     * @param string $type
     * @param string $stockNo
     * @return array
     */
    public function bindPubSubInData(string $type, string $stockNo): array
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
                "type"    => $type,
                "stockNo" => $stockNo,
                "error"   => $th->getMessage(),
            ];
            debug_log(json_encode($errorMsg, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "error-bindInData");
        }

        return $payload;
    }

    /**
     * @func bindPubSubOutData
     * @description '출고정보 pub/sub 메세지'
     * @param string $type
     * @param string $orderId
     * @param string $channelOrderId
     * @return array
     */
    public function bindPubSubOutData(string $type, string $orderId, string $channelOrderId): array
    {
        $payload = [];

        try {
            $outObjs = BonaeraOutBaseData::with([
                "out_options", "out_extras", "out_delivery", "out_extras", "out_weight",
                "out_boxs", "out_delivery_extras"
            ])
            ->where("order_id", $orderId)
            ->where("channel_order_id", $channelOrderId)
            ->get();

            $payload = [
                'type'             => $type,
                'channel'          => MallConstant::MALL_ONCHANNEL,
                'order_id'         => $orderId,
                'offer_id'         => $outObjs->first()->order->offer_id,
                'channel_order_id' => $channelOrderId,
            ];

            $outDatas     = [];
            $deiveryDatas = [];
            foreach ($outObjs as $outObj) {
                $outData = [
                    'sh_no'            => $outObj->sh_no,
                    'group_no'         => $outObj->group_no,
                    'stock_no'         => $outObj->stock_no,
                    'state'            => $outObj->state,
                    'memo'             => $outObj->memo,
                    'out_ordered_at'   => $outObj->out_ordered_at,
                    'out_completed_at' => $outObj->out_completed_at,
                ];

                $itemInfos = [];
                foreach ($outObj->out_options as $outOption) {
                    $itemInfo = [
                        'option_id'   => $outOption->option_id,
                        'it_code'     => $outOption->it_code,
                        'quantity'    => $outOption->quantity,
                        'shipped_qty' => $outOption->shipped_qty,
                    ];
                    $itemInfos[] = $itemInfo;
                }
                $outData['items_infos'] = $itemInfos;

                $outExtraServices = [];
                foreach ($outObj->out_extras as $outExtra) {
                    $outExtraService = [
                        'extra_name'  => $outExtra->extra_name ?? "",
                        'extra_money' => $outExtra->extra_money ?? 0.0,
                        'extra_cnt'   => $outExtra->extra_cnt ?? 0,
                    ];
                    $outExtraServices[] = $outExtraService;
                }
                $outData['out_extra_services'] = $outExtraServices;

                $deiveryData = [
                    'group_no'         => $outObj->out_delivery->group_no ?? "",
                    'receiver_name'    => $outObj->out_delivery->receiver_name ?? "",
                    'zip_code'         => $outObj->out_delivery->zip_code ?? "",
                    'addr1'            => $outObj->out_delivery->addr1 ?? "",
                    'addr2'            => $outObj->out_delivery->addr2 ?? "",
                    'receiver_phone'   => $outObj->out_delivery->receiver_phone ?? "",
                    'personal_type'    => $outObj->out_delivery->personal_type ?? "",
                    'personal_num'     => $outObj->out_delivery->personal_num ?? "",
                    'unipass_result'   => $outObj->out_delivery->unipass_result ?? "",
                    'unipass_reason'   => $outObj->out_delivery->unipass_reason ?? "",
                    'ctr_num'          => $outObj->out_delivery->ctr_num ?? "",
                    'state'            => $outObj->out_delivery->state ?? "",
                    'invoice'          => $outObj->out_delivery->invoice ?? "",
                    'box_cnt'          => $outObj->out_weight->box_cnt ?? 0,
                    'weight'           => $outObj->out_weight->weight ?? 0.0,
                    'ship_money'       => $outObj->out_weight->ship_money ?? 0,
                    'weight_fee'       => $outObj->out_weight->weight_fee ?? 0,
                    'volume_fee'       => $outObj->out_weight->volume_fee ?? 0,
                    'scv_money1'       => $outObj->out_weight->scv_money1 ?? 0,
                    'scv_money2'       => $outObj->out_weight->scv_money2 ?? 0,
                    'plus_money'       => $outObj->out_weight->plus_money ?? 0,
                    'plus_money_memo'  => $outObj->out_weight->plus_money_memo ?? "",
                    'minus_money'      => $outObj->out_weight->minus_money ?? 0,
                    'minus_money_memo' => $outObj->out_weight->minus_money_memo ?? "",
                    'commission'       => $outObj->out_weight->commission ?? 0,
                    'islands'          => $outObj->out_weight->islands ?? 0,
                    'total_money'      => $outObj->out_weight->total_money ?? 0,
                    'ship_memo'        => $outObj->out_weight->ship_memo ?? "",
                ];

                $boxInfos = [];
                foreach ($outObj->out_boxs as $outBox) {
                    $boxInfo = [
                        'real_weight' => $outBox->real_weight ?? 0.0,
                        'width'       => $outBox->width ?? 0.0,
                        'length'      => $outBox->length ?? 0.0,
                        'height'      => $outBox->height ?? 0.0,
                    ];
                    $boxInfos[] = $boxInfo;
                }
                $deiveryData['box_infos'] = $boxInfos;

                $deliveryExtraServices = [];
                foreach ($outObj->out_delivery_extras as $outDeliveryExtra) {
                    $outDeliveryExtra = [
                        'extra_name'  => $outDeliveryExtra->extra_name ?? "",
                        'extra_money' => $outDeliveryExtra->extra_money ?? 0.0,
                        'extra_cnt'   => $outDeliveryExtra->extra_cnt ?? 0,
                    ];
                    $deliveryExtraServices[] = $outDeliveryExtra;
                }
                $deiveryData['delivery_extra_services'] = $deliveryExtraServices;

                $outDatas[]     = $outData;
                $deiveryDatas[] = $deiveryData;
            }

            $payload["out_datas"] = $outDatas;
            $payload["deivery_datas"] = $deiveryDatas;
        } catch (Throwable $th) {
            $errorMsg = [
                "type"           => $type,
                "orderId"        => $orderId,
                "channelOrderId" => $channelOrderId,
                "error"          => $th->getMessage(),
            ];
            debug_log(json_encode($errorMsg, JSON_UNESCAPED_UNICODE), "kafka/wms-log", "error-bindOutData");
        }

        return $payload;
    }
}
