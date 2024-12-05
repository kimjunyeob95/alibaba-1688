<?php

namespace App\Services\Wms;

use App\Abstracts\WmsAbstract;
use App\Constants\BonaeraConstant;
use App\Constants\BonaeraErrorMessageConstant;
use App\Constants\OrderErrorMessageConstant;
use App\Constants\SlackConstant;
use App\Constants\WmsConstant;
use App\Events\BonaeraEvent;
use App\Http\Request\Bonaera\BonaeraOutDeliveryUpdateRequest;
use App\Models\BonaeraInBaseData;
use App\Models\BonaeraInFailData;
use App\Models\BonaeraInProductData;
use App\Models\BonaeraInProductImgData;
use App\Models\BonaeraOutBaseData;
use App\Models\BonaeraOutBoxData;
use App\Models\BonaeraOutDeliveryData;
use App\Models\BonaeraOutDeliveryExtraData;
use App\Models\BonaeraOutDeliveryPayLogData;
use App\Models\BonaeraOutExtraData;
use App\Models\BonaeraOutWeightData;
use App\Models\HsCodeData;
use App\Models\OrderChannelData;
use App\Packages\Bonaera;
use App\Packages\JwtPackage;
use App\Packages\Kafka;
use App\Packages\Slack;
use App\Vo\Bonaera\BonaeraEventDto;
use App\Vo\Bonaera\BonaeraOutBoxDataDto;
use App\Vo\Bonaera\BonaeraOutDeliveryDataDto;
use App\Vo\Bonaera\BonaeraOutWeightDataDto;
use Carbon\Carbon;
use Exception;
use SimpleXMLElement;
use Throwable;

class WmsW1 extends WmsAbstract
{
    public function __construct(Bonaera $bonaera, JwtPackage $jwtPackage, Slack $slack, Kafka $kafka)
    {
        parent::__construct($bonaera, $jwtPackage, $slack, $kafka);
    }

    public function hsCodeList(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $page       = $params["page"];
            $pageSize   = $params["pageSize"];
            $search_cls = $params["search_cls"];
            $keyword    = $params["keyword"];
            $sortArr    = explode("|", $params["sort"]);

            $builder = HsCodeData::query();

            if( !empty($keyword) ){
                if( $search_cls == WmsConstant::HSCODE_SEARCH_TYPE_KO ){
                    $builder->where("ko_name", "like", "%" . $keyword . "%")
                    ->orWhere("property_code_name", "like", "%" . $keyword . "%");
                } else if( $search_cls == WmsConstant::HSCODE_SEARCH_TYPE_EN ){
                    $builder->where("en_name", "like", "%" . $keyword . "%");
                } else if( $search_cls == WmsConstant::HSCODE_SEARCH_TYPE_HSCODE || $search_cls == WmsConstant::HSCODE_SEARCH_TYPE_SH_NO ){
                    $keyword = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
                    $keyword = explode(",", $keyword);
                    // 각 배열 요소의 앞뒤 공백 제거
                    $keyword = array_map('trim', $keyword);
                    // 빈 값을 제거
                    $keyword = array_filter($keyword);
                    // 중복 제거
                    $keyword = array_unique($keyword);

                    if( $search_cls == WmsConstant::HSCODE_SEARCH_TYPE_HSCODE ){
                        $builder->whereIn("hs_code", $keyword);
                    } else if( $search_cls == WmsConstant::HSCODE_SEARCH_TYPE_SH_NO ){
                        $builder->whereIn("sh_no", $keyword);
                    }
                }
            }

            $builder->orderByRaw("CASE WHEN {$sortArr[0]} = '' OR {$sortArr[0]} IS NULL THEN 1 ELSE 0 END, {$sortArr[0]} {$sortArr[1]}");

            $lists = $builder->paginate($pageSize, ['*'], 'page', $page)->appends($params);

            $returnMsg = helpers_success_message($lists);
        } catch (Throwable $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function inList(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $pageSize   = $params["pageSize"];
            $status     = $params["status"];
            $timeCls    = $params["timeCls"];
            $startTime  = $params["startTime"];
            $endTime    = $params["endTime"];
            $search_cls = $params["search_cls"];
            $keyword    = $params["keyword"];
            $sortArr    = explode("|", $params["sort"]);

            $builder = BonaeraInBaseData::select([
                "bonaera_in_base_datas.*",
                "ocd.channel_order_id"
            ])
            ->with(["product", "logistics_last", "in_options.w_option"])
            ->leftJoin("bonaera_in_product_datas as bipd", "bonaera_in_base_datas.stock_no", "=", "bipd.stock_no")
            ->leftJoin("order_channel_datas as ocd", "bonaera_in_base_datas.order_id", "=", "ocd.order_id")
            ->groupBy("bonaera_in_base_datas.stock_no");

            if( !empty($status) ){
                $builder->where("bipd.status", $status);
            }
            if( !empty($timeCls) ){
                if( $timeCls == "create" ){
                    if( !empty($startTime) ) {
                        $builder->where("bonaera_in_base_datas.created_at", ">=", $startTime . " 00:00:00");
                    }
                    if( !empty($endTime) ) {
                        $builder->where("bonaera_in_base_datas.created_at", "<=", $endTime . " 23:59:59");
                    }
                } else if( $timeCls == "complete" ){
                    if( !empty($startTime) ) {
                        $builder->where("bonaera_in_base_datas.completed_at", ">=", $startTime . " 00:00:00");
                    }
                    if( !empty($endTime) ) {
                        $builder->where("bonaera_in_base_datas.completed_at", "<=", $endTime . " 23:59:59");
                    }
                }
            }
            if( !empty($keyword) ){
                $keyword = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
                $keyword = explode(",", $keyword);
                // 각 배열 요소의 앞뒤 공백 제거
                $keyword = array_map('trim', $keyword);
                // 빈 값을 제거
                $keyword = array_filter($keyword);
                // 중복 제거
                $keyword = array_unique($keyword);

                if( $search_cls == WmsConstant::IN_SEARCH_TYPE_STOCK_NO ){
                    $builder->whereIn("bonaera_in_base_datas.stock_no", $keyword);
                } else if( $search_cls == WmsConstant::IN_SEARCH_TYPE_ORDER_ID ){
                    $builder->whereIn("bonaera_in_base_datas.order_id", $keyword);
                } else if( $search_cls == WmsConstant::IN_SEARCH_TYPE_CHANNEL_ORDER_ID ){
                    $builder->whereIn("ocd.channel_order_id", $keyword);
                }
            }

            $builder->orderBy("bonaera_in_base_datas." . $sortArr[0], $sortArr[1]);

            $lists = $builder->paginate($pageSize)->appends($params);

            $returnMsg = helpers_success_message($lists);
        } catch (Throwable $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function inFailList(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $pageSize   = $params["pageSize"];
            $timeCls    = $params["timeCls"];
            $startTime  = $params["startTime"];
            $endTime    = $params["endTime"];
            $search_cls = $params["search_cls"];
            $keyword    = $params["keyword"];
            $sortArr    = explode("|", $params["sort"]);

            $builder = BonaeraInFailData::select([
                "bonaera_in_fail_datas.*",
                "ocd.buyer_name",
                "obd.channel",
                "ocd.channel_order_id"
            ])
            ->with(["order.product", "logistics_last", "w_options.option"])
            ->leftJoin("order_base_datas as obd", "bonaera_in_fail_datas.order_id", "=", "obd.order_id")
            ->leftJoin("order_channel_datas as ocd", "bonaera_in_fail_datas.order_id", "=", "ocd.order_id")
            ->leftJoin("bonaera_in_base_datas as bibd", "bonaera_in_fail_datas.order_id", "=", "bibd.order_id");

            $builder->whereNull("bibd.id");

            if( !empty($status) ){
                $builder->where("bipd.status", $status);
            }
            if( !empty($timeCls) ){
                if( $timeCls == "create" ){
                    if( !empty($startTime) ) {
                        $builder->where("bonaera_in_fail_datas.created_at", ">=", $startTime . " 00:00:00");
                    }
                    if( !empty($endTime) ) {
                        $builder->where("bonaera_in_fail_datas.created_at", "<=", $endTime . " 23:59:59");
                    }
                } else if( $timeCls == "modi" ){
                    if( !empty($startTime) ) {
                        $builder->where("bonaera_in_fail_datas.updated_at", ">=", $startTime . " 00:00:00");
                    }
                    if( !empty($endTime) ) {
                        $builder->where("bonaera_in_fail_datas.updated_at", "<=", $endTime . " 23:59:59");
                    }
                }
            }
            if( !empty($keyword) ){
                $keyword = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
                $keyword = explode(",", $keyword);
                // 각 배열 요소의 앞뒤 공백 제거
                $keyword = array_map('trim', $keyword);
                // 빈 값을 제거
                $keyword = array_filter($keyword);
                // 중복 제거
                $keyword = array_unique($keyword);

                if( $search_cls == WmsConstant::IN_FAIL_SEARCH_TYPE_ORDER_ID ){
                    $builder->whereIn("bonaera_in_fail_datas.order_id", $keyword);
                } else if( $search_cls == WmsConstant::IN_FAIL_SEARCH_TYPE_CHANNEL_ORDER_ID ){
                    $builder->whereIn("ocd.channel_order_id", $keyword);
                } else if( $search_cls == WmsConstant::IN_FAIL_SEARCH_TYPE_OFFER_ID ){
                    $builder->whereIn("obd.offer_id", $keyword);
                }
            }

            $builder->orderBy("obd." . $sortArr[0], $sortArr[1]);

            $lists = $builder->paginate($pageSize)->appends($params);

            $returnMsg = helpers_success_message($lists);
        } catch (Throwable $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function getTariff(string $hsCode): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $endPoint = "https://unipass.customs.go.kr:38010/ext/rest/trrtQry/retrieveTrrt";
            $payload = [
                "crkyCn"   => $this->unipass_token,
                // "trrtTpcd" => "FEU1",
                "hsSgn"    => $hsCode
            ];

            $res    = [];
            $result = helpers_curl("GET", $endPoint, [], $payload);

            // XML을 SimpleXMLElement 객체로 변환
            $xml = new SimpleXMLElement($result);
            // XML을 배열로 변환
            $array = json_decode(json_encode((array)$xml), true);

            if( !isset($array["trrtQryRsltVo"]) ){
                $msg = "unipass 통신 에러";

                if( isset($array["ntceInfo"]) && $array["ntceInfo"] ){
                    $msg = $array["ntceInfo"];
                }
                throw new Exception($msg);   
            }

            /** 이차원 배열인지 체크 */
            $isMultidimensional = isset($array["trrtQryRsltVo"][0]) && is_array($array["trrtQryRsltVo"][0]);
            if( $isMultidimensional === true ){
                $res = $array["trrtQryRsltVo"];
            } else {
                $res[] = $array["trrtQryRsltVo"];
            }

            $returnMsg = helpers_success_message($res);
        } catch (Throwable $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function bonaeraInHscodeUpdate(array $ids, string $hsCode): array
    {
        $returnMsg = helpers_fail_message();

        try {
            $inBaseObjs = BonaeraInBaseData::whereIn("id", $ids)->get();
            foreach ($inBaseObjs as $inBaseObj) {
                BonaeraInProductData::where("stock_no", $inBaseObj->stock_no)->update([
                    "hs_code" => $hsCode
                ]);
            }

            $returnMsg = helpers_success_message();
        } catch (Throwable $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function bonaeraInFailHscodeUpdate(array $ids, string $hsCode): array
    {
        $returnMsg = helpers_fail_message();

        try {
            BonaeraInFailData::whereIn("id", $ids)->update([
                "hs_code" => $hsCode
            ]);

            $returnMsg = helpers_success_message();
        } catch (Throwable $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function bonaeraInFailCreate(int $id): void
    {
        try {
            $inFailObj = BonaeraInFailData::where("id", $id)->first();
            if( $inFailObj === null ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_IN_FAIL_DATA"));
            }

            $result = $this->bonaera->createStockApi($inFailObj->order_id);

            if( $result["isSuccess"] === true ){
                $bonaeraEventDtoBind = [
                    'type'    => WmsConstant::WMS_CODE_TYPE_IT000,
                    'stockNo' => $result["data"]["stock_no"],
                ];
                $bonaeraEventDto = new BonaeraEventDto();
                $bonaeraEventDto->bind($bonaeraEventDtoBind);
                event(new BonaeraEvent($bonaeraEventDto));
            }
        } catch (Throwable $e) {
            $msg = "error: " . $e->getMessage(). " | id: " . $id;
            debug_log($msg, "boneara/bonaeraInFailCreate", "bonaeraInFailCreate");
        }
    }

    public function bonaeraInUpdate(int $id): void
    {
        try {
            $inBaseObj = BonaeraInBaseData::where("id", $id)->first();
            if( $inBaseObj == null ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_IN_BASE_DATA"));
            }

            /** 재고현황 조회 */
            $res = $this->bonaera->getStockList($inBaseObj->stock_no);
            if( $res["isSuccess"] === true && isset($res["data"]["appCode"]) && isset($res["data"]["appitemList"]) ) {
                $stockCode       = $res["data"]["appCode"];
                $lastCompletedAt = null;
                $isStatusChange  = false;
                foreach ($res["data"]["appitemList"] as $item) {
                    if( !empty($item["itemIndate"]) ){
                        $currentDate = Carbon::parse($item["itemIndate"]);
                        if ( $lastCompletedAt === null || $currentDate->gt($lastCompletedAt) ) { // 가장 최근 날짜로 할당
                            $lastCompletedAt = $currentDate;
                        }
                    }

                    $inProductObj = BonaeraInProductData::where([
                        "stock_no" => $stockCode,
                        "order_id" => $inBaseObj->order_id,
                        "it_code"  => $item["itemCode"],
                    ])->first();
                    
                    if ($inProductObj !== null) {
                        $inProductObj->update([
                            "received_qty"  => $item["itemCount"],
                            "stock_qty"     => $item["itemLimitCnt"],
                            "status"        => $item["itemState"],
                            "memo"          => $item["itemInMemo"] ?? "",
                            "in_comming_at" => $item["itemIndate"] ?? null,
                        ]);
                    }

                    if( $inProductObj === null || ( $isStatusChange === false && $inProductObj->status != $item["itemState"] ) ){
                        $isStatusChange = true;
                    }

                    for ($i = 1; $i < 11; $i ++) { 
                        $imgKey = "itemImg" . $i;
                        if (isset($item[$imgKey]) && !empty($item[$imgKey])) {
                            BonaeraInProductImgData::updateOrCreate(
                                [
                                    "product_id" => $inProductObj->id,
                                    "img_number" => constant("App\Constants\BonaeraConstant::IMG_NUMBER_" . $i)
                                ],
                                [
                                    "img_url" => $item[$imgKey]
                                ]
                            );
                        }
                    }
                }

                $inBaseObj->update([
                    "completed_at" => $lastCompletedAt
                ]);

                if( $isStatusChange === true ){
                    $this->bonaeraInUpdateSendSlack($inBaseObj);
                }
            }
        } catch (Throwable $e) {
            $msg = "error: " . $e->getMessage() . " | id: {$id}";
            debug_log($msg, "boneara/bonaeraUpdate", "bonaeraInUpdate");
        }
    }

    public function bonaeraInUpdateSendSlack(BonaeraInBaseData $inBaseObj): void
    {
        $isSendSlack         = false;
        $allInStatusRecieved = true;
        $webhookUrl          = SlackConstant::WMS_INFO_SLACK();
        $message             = "[WMS 입고 알림]\n";
        $channelObj          = OrderChannelData::where("order_id", $inBaseObj->order_id)->first();
        $inProductObjs       = BonaeraInProductData::with(["w_option"])->where([
            "stock_no" => $inBaseObj->stock_no,
            "order_id" => $inBaseObj->order_id,
        ])->get();

        if( $channelObj !== null && count($inProductObjs) > 0 ){
            foreach ($inProductObjs as $inProductObj) {
                if( in_array($inProductObj->status, [BonaeraConstant::WAREHOUSE_STATUS_RECEIVED, BonaeraConstant::WAREHOUSE_STATUS_ERROR]) ){
                    $isSendSlack = true;
                    break;
                }
            }
            if( $isSendSlack === true ){
                foreach ($inProductObjs as $inProductObj) {
                    if( $inProductObj->status !== BonaeraConstant::WAREHOUSE_STATUS_RECEIVED ){
                        $allInStatusRecieved = false;
                        break;
                    }
                }

                if( $allInStatusRecieved === true ){
                    $message .= "상태 : 정상입고\n";
                } else {
                    $message .= "상태 : 오류입고\n";
                }
                $message .= "채널 주문번호 : {$channelObj->channel_order_id}\n";
                $message .= "WAPP 주문 번호 : {$inBaseObj->order_id}\n";
                $message .= "입고번호 : {$inBaseObj->stock_no}\n";
                $optCnt   = 1;
                foreach ($inProductObjs as $inProductObj) {
                    if( $inProductObj->w_option ){
                        $message .= $optCnt . ". " . $inProductObj->w_option->option_name_kr . " ({$inProductObj->quantity}) : " . BonaeraConstant::WAREHOUSE_STATUS[$inProductObj->status] . "\n";
                        $optCnt++;
                    }
                }
                $res = $this->slack->sendMessage($webhookUrl, $message);
                if( $res["isSuccess"] === false ){
                    $msg = "error: " . $res["msg"] . " | stockNo: {$inBaseObj->stock_no}";
                    debug_log($msg, "boneara/bonaeraSlack", "bonaeraInUpdateSendSlack");
                }
            }
        }
    }

    public function inDetail(string $stockNo): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $res = BonaeraInBaseData::with([
                "in_options.imgs",
                "product",
                "logistics_last"
            ])
            ->where("stock_no", $stockNo)->first();

            $returnMsg = helpers_success_message($res);
        } catch (Throwable $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function outSignList(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $pageSize   = $params["pageSize"];
            $status     = $params["status"];
            $inStatus   = $params["inStatus"];
            $timeCls    = $params["timeCls"];
            $startTime  = $params["startTime"];
            $endTime    = $params["endTime"];
            $search_cls = $params["search_cls"];
            $keyword    = $params["keyword"];
            $sortArr    = explode("|", $params["sort"]);

            $builder = OrderChannelData::select([
                "order_channel_datas.*",
                "obd.channel",
                "bobd.sh_no",
                "bobd.group_no",
                "bobd.created_at as out_created_at",
                "bofd.updated_at as fail_updated_at",
                "bofd.msg",
            ])
            ->with(["details.option", "boneara_in_base.in_options", "order.product"])
            ->join("order_base_datas as obd", "order_channel_datas.order_id", "=", "obd.order_id")
            ->leftJoin("bonaera_out_base_datas as bobd", "order_channel_datas.order_id", "=", "bobd.order_id")
            ->leftJoin("bonaera_in_base_datas as bibd", "order_channel_datas.order_id", "=", "bibd.order_id")
            ->join("bonaera_in_product_datas as bipd", "order_channel_datas.order_id", "=", "bipd.order_id")
            ->leftJoin("bonaera_out_fail_datas as bofd", "order_channel_datas.order_id", "=", "bofd.order_id")
            ->groupBy("order_channel_datas.order_id")
            ->orderBy("order_channel_datas." . $sortArr[0], $sortArr[1]);

            if( !empty($status) ){
                if( $status == WmsConstant::OUT_SIGN_STATUS_SUCCESS ){
                    $builder->whereNotNull("bobd.sh_no");
                } else if( $status == WmsConstant::OUT_SIGN_STATUS_FAIL ){
                    $builder->whereNull("bobd.sh_no")
                    ->whereNotNull("bofd.msg");
                } else if( $status == WmsConstant::OUT_SIGN_STATUS_WAIT ){
                    $builder->whereNull("bobd.sh_no")
                    ->whereNull("bofd.msg");
                }
            }
            if( !empty($inStatus) ){
                $builder->where("bipd.status", $inStatus);
            }
            if( !empty($timeCls) ){
                if( $timeCls == "create" ){
                    if( !empty($startTime) ) {
                        $builder->where("order_channel_datas.created_at", ">=", $startTime . " 00:00:00");
                    }
                    if( !empty($endTime) ) {
                        $builder->where("order_channel_datas.created_at", "<=", $endTime . " 23:59:59");
                    }
                } else if( $timeCls == "modi" ){
                    if( !empty($startTime) ) {
                        $builder->where("order_channel_datas.updated_at", ">=", $startTime . " 00:00:00");
                    }
                    if( !empty($endTime) ) {
                        $builder->where("order_channel_datas.updated_at", "<=", $endTime . " 23:59:59");
                    }
                }
            }
            if( !empty($keyword) ){
                $keyword = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
                $keyword = explode(",", $keyword);
                // 각 배열 요소의 앞뒤 공백 제거
                $keyword = array_map('trim', $keyword);
                // 빈 값을 제거
                $keyword = array_filter($keyword);
                // 중복 제거
                $keyword = array_unique($keyword);

                if( $search_cls == WmsConstant::OUT_SIGN_SEARCH_TYPE_ORDER_ID ){
                    $builder->whereIn("order_channel_datas.order_id", $keyword);
                } else if( $search_cls == WmsConstant::OUT_SIGN_SEARCH_TYPE_CHANNEL_ORDER_ID ){
                    $builder->whereIn("order_channel_datas.channel_order_id", $keyword);
                } else if( $search_cls == WmsConstant::OUT_SIGN_SEARCH_TYPE_STOCK_NO ){
                    $builder->where(function($qry) use($keyword) {
                        $qry->whereIn("bobd.stock_no", $keyword)
                        ->orWhereIn("bibd.stock_no", $keyword);
                    });
                } else if( $search_cls == WmsConstant::OUT_SIGN_SEARCH_TYPE_SH_NO ){
                    $builder->whereIn("bobd.sh_no", $keyword);
                } else if( $search_cls == WmsConstant::OUT_SIGN_SEARCH_TYPE_GROUP_NO ){
                    $builder->whereIn("bobd.group_no", $keyword);
                }
            }

            $lists     = $builder->paginate($pageSize)->appends($params);
            $returnMsg = helpers_success_message($lists);
            // dd($lists->toArray());
        } catch (Throwable $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }
        return $returnMsg;
    }

    public function outList(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $pageSize     = $params["pageSize"];
            $status       = $params["status"];
            $personalType = $params["personalType"];
            $shippingType = $params["shippingType"];
            $unipassType  = $params["unipassType"];
            $timeCls      = $params["timeCls"];
            $startTime    = $params["startTime"];
            $endTime      = $params["endTime"];
            $search_cls   = $params["search_cls"];
            $keyword      = $params["keyword"];
            $sortArr      = explode("|", $params["sort"]);

            $builder = BonaeraOutBaseData::select([
                "bonaera_out_base_datas.*",
                "bodd.state as delivery_state",
                "bodd.invoice",
                "bodd.receiver_name",
                "bodd.personal_num",
                "bodd.personal_type",
                "bodd.unipass_reason",
                "bodd.ctr_num",
                "ocd.shipping_type",
            ])
            ->with(["order.product", "logistics_last", "out_options.w_option", "out_weight", "pay_fail_log"])
            ->leftJoin("bonaera_out_delivery_datas as bodd", "bonaera_out_base_datas.group_no", "=", "bodd.group_no")
            ->leftJoin("order_channel_datas as ocd", "bonaera_out_base_datas.order_id", "=", "ocd.order_id")
            ->groupBy("bonaera_out_base_datas.group_no");

            if( !empty($status) ){
                switch ($status) {
                    case WmsConstant::OUT_LIST_STATUS_302_303:
                        $builder->where("bonaera_out_base_datas.state", BonaeraConstant::GROUP_STATUS_302);
                        $builder->where("bodd.state", BonaeraConstant::GROUP_STATUS_303);    
                        break;
                    case WmsConstant::OUT_LIST_STATUS_302_304:
                        $builder->where("bonaera_out_base_datas.state", BonaeraConstant::GROUP_STATUS_302);
                        $builder->where("bodd.state", BonaeraConstant::GROUP_STATUS_304);    
                        break;
                    case WmsConstant::OUT_LIST_STATUS_302_305:
                        $builder->where("bonaera_out_base_datas.state", BonaeraConstant::GROUP_STATUS_302);
                        $builder->where("bodd.state", BonaeraConstant::GROUP_STATUS_305);    
                        break;
                    case WmsConstant::OUT_LIST_STATUS_302_306:
                        $builder->where("bonaera_out_base_datas.state", BonaeraConstant::GROUP_STATUS_302);
                        $builder->where("bodd.state", BonaeraConstant::GROUP_STATUS_306);    
                        break;
                    case WmsConstant::OUT_LIST_STATUS_302_307:
                        $builder->where("bonaera_out_base_datas.state", BonaeraConstant::GROUP_STATUS_302);
                        $builder->where("bodd.state", BonaeraConstant::GROUP_STATUS_307);    
                        break;
                    case WmsConstant::OUT_LIST_STATUS_300:
                        $builder->where("bonaera_out_base_datas.state", BonaeraConstant::GROUP_STATUS_300);
                        break;
                    default:
                        break;
                }
            }
            if( !empty($personalType) ){
                $builder->where("bodd.personal_type", $personalType);
            }
            if( !empty($shippingType) ){
                $builder->where("bodd.ctr_num", $shippingType);
            }
            if( !empty($unipassType) ){
                switch ($unipassType) {
                    case BonaeraConstant::UNIPASS_RESULT_SUCCESS:
                        $builder->where("bodd.unipass_result", "!=", BonaeraConstant::UNIPASS_RESULT_0);
                        break;
                    case BonaeraConstant::UNIPASS_RESULT_FAIL:
                        $builder->where("bodd.unipass_result", BonaeraConstant::UNIPASS_RESULT_0);
                        break;
                    default:
                        break;
                }
            }
            if( !empty($timeCls) ){
                if( $timeCls == "order" ){
                    if( !empty($startTime) ) {
                        $builder->where("bonaera_out_base_datas.out_ordered_at", ">=", $startTime . " 00:00:00");
                    }
                    if( !empty($endTime) ) {
                        $builder->where("bonaera_out_base_datas.out_ordered_at", "<=", $endTime . " 23:59:59");
                    }
                } else if( $timeCls == "complete" ){
                    if( !empty($startTime) ) {
                        $builder->where("bonaera_out_base_datas.out_completed_at", ">=", $startTime . " 00:00:00");
                    }
                    if( !empty($endTime) ) {
                        $builder->where("bonaera_out_base_datas.out_completed_at", "<=", $endTime . " 23:59:59");
                    }
                }
            }
            if( !empty($keyword) ){
                $keyword = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
                $keyword = explode(",", $keyword);
                // 각 배열 요소의 앞뒤 공백 제거
                $keyword = array_map('trim', $keyword);
                // 빈 값을 제거
                $keyword = array_filter($keyword);
                // 중복 제거
                $keyword = array_unique($keyword);

                if( $search_cls == WmsConstant::OUT_SEARCH_TYPE_SH_NO ){
                    $builder->whereIn("bonaera_out_base_datas.sh_no", $keyword);
                } else if( $search_cls == WmsConstant::OUT_SEARCH_TYPE_GROUP_NO ){
                    $builder->whereIn("bonaera_out_base_datas.group_no", $keyword);
                } else if( $search_cls == WmsConstant::OUT_SEARCH_TYPE_CHANNEL_ORDER_ID ){
                    $builder->whereIn("ocd.channel_order_id", $keyword);
                } else if( $search_cls == WmsConstant::OUT_SEARCH_TYPE_INVOICE ){
                    $builder->whereIn("bodd.invoice", $keyword);
                }
            }

            $builder->orderBy("bonaera_out_base_datas." . $sortArr[0], $sortArr[1]);

            $groupNos     = (clone $builder)->pluck('bonaera_out_base_datas.group_no');
            $otherObjsMap = BonaeraOutBaseData::select([
                "bonaera_out_base_datas.*",
            ])->with([
                "order.product",
                "out_options.w_option"
            ])
            ->whereIn("bonaera_out_base_datas.group_no", $groupNos)
            ->get()
            ->groupBy('group_no');

            $lists = $builder->paginate($pageSize)->appends($params);

            foreach ($lists as &$data) {
                $otherObjs = collect($otherObjsMap->get($data->group_no, []))
                    ->filter(function($item) use ($data) {
                        return $item->id !== $data->id;
                    })
                    ->values();
                
                $data["otherObjs"] = $otherObjs;
            }

            // dd($lists->toArray());
            $returnMsg = helpers_success_message($lists);
        } catch (Throwable $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function bonaeraOutCreate(int $id): void
    {
        try {
            $channelObj = OrderChannelData::where("id", $id)->first();
            if( $channelObj === null ){
                throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_CHANNEL"));
            }

            /** 출고신청 */
            $result = $this->bonaera->createApplicationApi($channelObj->order_id);

            /** 출고신청 업데이트 */
            $outBaseObj = BonaeraOutBaseData::where("order_id", $channelObj->order_id)->first();
            if( $outBaseObj !== null ){
                $this->bonaeraOutUpdate($outBaseObj->id);
            }

            if( $result["isSuccess"] === true ){
                $bonaeraEventDtoBind = [
                    'type'           => WmsConstant::WMS_CODE_TYPE_SH000,
                    'stockNo'        => $result["data"]["stock_no"],
                    'orderId'        => $channelObj->order_id,
                    'channelOrderId' => $channelObj->channel_order_id,
                ];
                $bonaeraEventDto = new BonaeraEventDto();
                $bonaeraEventDto->bind($bonaeraEventDtoBind);
                event(new BonaeraEvent($bonaeraEventDto));
            }
        } catch (Throwable $e) {
            $msg = "error: " . $e->getMessage() . " | id: {$id}";
            debug_log($msg, "boneara/bonaeraOutCreate", "bonaeraOutCreate");
        }
    }

    public function bonaeraOutUpdate(int $id): void
    {
        try {
            $baseObj = BonaeraOutBaseData::where("id", $id)->first();
            if( $baseObj == null ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_OUT_BASE_DATA"));
            }

            $groupNo = $baseObj->group_no;

            /** 신청서 조회 */
            $res = $this->bonaera->getApplicationList($groupNo);
            if( $res["isSuccess"] === true && isset($res["data"]["data"]["grCode"]) && isset($res["data"]["data"]["ReciverInfo"][0]) ) {
                $res         = $res["data"]["data"];
                $reciverInfo = $res["ReciverInfo"][0];
                $deliveryObj = BonaeraOutDeliveryData::where("group_no", $groupNo)->first();
                $isSendSlack = false;
                if( $deliveryObj !== null ){
                    if( !in_array($deliveryObj->state, [BonaeraConstant::GROUP_STATUS_304, BonaeraConstant::GROUP_STATUS_307]) && 
                        in_array($res["state"], [BonaeraConstant::GROUP_STATUS_304, BonaeraConstant::GROUP_STATUS_307])    
                    ){
                        $isSendSlack = true;
                    }
                }

                $bonaeraOutDeliveryDataDtoBind = [
                    "groupNo"       => $groupNo,
                    "invoice"       => $res["invoice"] ?? "",
                    "ctrNum"        => $res["ctrNum"] ?? BonaeraConstant::CTR_NUM_2,
                    "state"         => $res["state"],
                    "receiverName"  => $reciverInfo["receiverName"],
                    "zipCode"       => $reciverInfo["zipCode"],
                    "addr1"         => $reciverInfo["addr1"],
                    "addr2"         => $reciverInfo["addr2"],
                    "receiverPhone" => $reciverInfo["receiverPhone"],
                    "personalType"  => $reciverInfo["personalType"],
                    "personalNum"   => $reciverInfo["personalNum"],
                    "unipassResult" => $reciverInfo["unipassResult"],
                    "unipassReason" => $reciverInfo["unipassReason"],
                    "shipMemo"      => $reciverInfo["shipMemo"],
                ];
                $bonaeraOutDeliveryDataDto = new BonaeraOutDeliveryDataDto();
                $bonaeraOutDeliveryDataDto->bind($bonaeraOutDeliveryDataDtoBind);

                $upsertWhere = $bonaeraOutDeliveryDataDto->getAllProperties();
                unset($upsertWhere["group_no"]);

                BonaeraOutDeliveryData::updateOrCreate(
                    ["group_no" => $groupNo],
                    $upsertWhere
                );

                BonaeraOutBaseData::where("group_no", $groupNo)->update([
                    "out_completed_at" => $res["outday"] ?? null
                ]);

                if( isset($res["ExtraSvcShip"]) ){
                    $existsExtras = [];
                    foreach ($res["ExtraSvcShip"] as $extra) {
                        if( !empty($extra["ExtraName"]) ){
                            $existsExtras[] = $extra["ExtraName"];
                            BonaeraOutDeliveryExtraData::updateOrCreate(
                                [
                                    "group_no"   => $groupNo,
                                    "extra_name" => $extra["ExtraName"],
                                ],
                                [
                                    "extra_money" => $extra["ExtraMoney"],
                                    "extra_cnt"   => $extra["ExtraCnt"],
                                ]
                            );
                        }
                    }

                    if( !empty($existsExtras)) {
                        BonaeraOutDeliveryExtraData::where("group_no", $groupNo)
                        ->whereNotIn("extra_name", $existsExtras)
                        ->forceDelete();
                    }
                }

                if( isset($res["weightList"][0]) ){
                    $weight  = $res["weightList"][0];
                    $boxList = $weight["boxList"] ?? [];

                    BonaeraOutBoxData::where("group_no", $groupNo)->forceDelete();

                    foreach ($boxList as $box) {
                        $bonaeraOutBoxDataDtoBind = [
                            "groupNo"        => $groupNo,
                            "boxCnt"         => 1,
                            "realWeight"     => !empty($box["realWeight"]) ? $box["realWeight"] : 0,
                            "width"          => !empty($box["width"]) ? $box["width"] : 0,
                            "length"         => !empty($box["length"]) ? $box["length"] : 0,
                            "height"         => !empty($box["height"]) ? $box["height"] : 0,
                        ];
                        $bonaeraOutBoxDataDto = new BonaeraOutBoxDataDto();
                        $bonaeraOutBoxDataDto->bind($bonaeraOutBoxDataDtoBind);
    
                        $insertWhere = $bonaeraOutBoxDataDto->getAllProperties();
                        BonaeraOutBoxData::create($insertWhere);
                    }

                    $bonaeraOutWeightDataDtoBind = [
                        "groupNo"        => $groupNo,
                        "boxCnt"         => !empty($weight["boxCnt"]) ? $weight["boxCnt"] : 0,
                        "weight"         => !empty($weight["weight"]) ? $weight["weight"] : 0,
                        "shipMoney"      => !empty($weight["shipMoney"]) ? $weight["shipMoney"] : 0,
                        "weightFee"      => !empty($weight["weightFee"]) ? $weight["weightFee"] : 0,
                        "volumeFee"      => !empty($weight["volumeFee"]) ? $weight["volumeFee"] : 0,
                        "svcMoney1"      => !empty($weight["svcMoney1"]) ? $weight["svcMoney1"] : 0,
                        "svcMoney2"      => !empty($weight["svcMoney2"]) ? $weight["svcMoney2"] : 0,
                        "plusMoney"      => !empty($weight["plusMoney"]) ? $weight["plusMoney"] : 0,
                        "plusMoneyMemo"  => $weight["plusMoneyMemo"] ?? "",
                        "minusMoney"     => !empty($weight["minusMoney"]) ? $weight["minusMoney"] : 0,
                        "minusMoneyMemo" => $weight["minusMoneyMemo"] ?? "",
                        "commission"     => !empty($weight["commission"]) ? $weight["commission"] : 0,
                        "islands"        => !empty($weight["islands"]) ? $weight["islands"] : 0,
                        "totalMoney"     => !empty($weight["totalMoney"]) ? $weight["totalMoney"] : 0,
                    ];
                    $bonaeraOutWeightDataDto = new BonaeraOutWeightDataDto();
                    $bonaeraOutWeightDataDto->bind($bonaeraOutWeightDataDtoBind);

                    $upsertWhere = $bonaeraOutWeightDataDto->getAllProperties();
                    unset($upsertWhere["group_no"]);

                    BonaeraOutWeightData::updateOrCreate(
                        ["group_no" => $groupNo],
                        $upsertWhere
                    );
                }

                if( $isSendSlack === true ){
                    $this->bonaeraOutUpdateSendSlack($groupNo);
                }
            }

            $baseObjs = BonaeraOutBaseData::where("group_no", $groupNo)->get();
            foreach ($baseObjs as $outObj) {
                $shNo = $outObj->sh_no;

                /** 상품정보조회(출고신청번호기준) 조회 */
                $orderRes = $this->bonaera->getOrderApplicationList($shNo);
                if( isset($orderRes["data"]["ExtraSvcOrder"]) ){
                    $existsExtras = [];
                    foreach ($orderRes["data"]["ExtraSvcOrder"] as $extraOrder) {
                        if( !empty($extraOrder["ExtraName"]) ){
                            $existsExtras[] = $extraOrder["ExtraName"];
                            BonaeraOutExtraData::updateOrCreate(
                                [
                                    "sh_no"      => $shNo,
                                    "extra_name" => $extraOrder["ExtraName"],
                                ],
                                [
                                    "extra_money" => $extraOrder["ExtraMoney"],
                                    "extra_cnt"   => $extraOrder["ExtraCnt"],
                                ]
                            );  
                        }
                    }

                    if( !empty($existsExtras)) {
                        BonaeraOutExtraData::where("sh_no", $shNo)
                        ->whereNotIn("extra_name", $existsExtras)
                        ->forceDelete();
                    }
                }

                if( isset($orderRes["data"]["orState"]) && !empty($orderRes["data"]["orState"]) ){
                    BonaeraOutBaseData::where("sh_no", $shNo)->update([
                        "state" => $orderRes["data"]["orState"]
                    ]);
                }
            }
        } catch (Throwable $e) {
            $msg = "error: " . $e->getMessage() . " | id: {$id}";
            debug_log($msg, "boneara/bonaeraUpdate", "bonaeraOutUpdate");
        }
    }

    public function bonaeraOutPay(int $id): void
    {
        try {
            $baseObj = BonaeraOutBaseData::with([
                "out_delivery"
            ])->where("id", $id)->first();
            if( $baseObj == null ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_OUT_BASE_DATA"));
            }

            if( $baseObj->out_delivery == null ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_OUT_DELIVERY_DATA"));
            }

            $groupNo = $baseObj->group_no;
            $payObj  = BonaeraOutDeliveryPayLogData::where([
                "group_no" => $groupNo,
                "success"  => BonaeraConstant::DELIVERY_PAY_Y
            ])->first();

            if( $baseObj->out_delivery->state === BonaeraConstant::GROUP_STATUS_304 && $payObj === null ){
                $res = $this->bonaera->getPayment($groupNo);
                if( $res["isSuccess"] === true && isset($res["data"]["groupNo"]) && $res["data"]["groupNo"] ) {
                    BonaeraOutDeliveryPayLogData::updateOrCreate(
                        [
                            "group_no" => $groupNo,
                        ],
                        [
                            "success" => BonaeraConstant::DELIVERY_PAY_Y,
                            "message" => $res["data"]["message"]
                        ]
                    );
                    $this->bonaeraOutUpdate($id);
                } else {
                    BonaeraOutDeliveryPayLogData::updateOrCreate(
                        [
                            "group_no" => $groupNo,
                        ],
                        [
                            "success" => BonaeraConstant::DELIVERY_PAY_N,
                            "message" => $res["msg"]
                        ]
                    );
                }
            }
        } catch (Throwable $e) {
            $msg = "error: " . $e->getMessage() . " | id: {$id}";
            debug_log($msg, "boneara/bonaeraOutPay", "bonaeraOutPay");
        }
    }

    public function bonaeraOutDeliveryUpdate(BonaeraOutDeliveryUpdateRequest $request): array
    {
        $returnMsg = $this->returnMsg;
        
        try {
            $outDeliveryObj = BonaeraOutDeliveryData::where("group_no", $request->groupNo)->first();
            if( $outDeliveryObj === null ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_OUT_DELIVERY_DATA"));
            }

            if( !in_array($outDeliveryObj->state, [BonaeraConstant::GROUP_STATUS_303, BonaeraConstant::GROUP_STATUS_304]) ){
                $errorMsg = "배송정보 수정은 " . BonaeraConstant::GROUP_STATUS[BonaeraConstant::GROUP_STATUS_303] . ", " . BonaeraConstant::GROUP_STATUS[BonaeraConstant::GROUP_STATUS_304] . " 상태만 가능합니다.";
                throw new Exception($errorMsg);
            }

            $result = $this->bonaera->applicationModifyApi($request);
            if( $result["isSuccess"] !== true ){
                throw new Exception($result["msg"]);
            }

            $baseOutObj = BonaeraOutBaseData::where("group_no", $request->groupNo)->first();
            $this->bonaeraOutUpdate($baseOutObj->id);

            $returnMsg = helpers_success_message();
        } catch (Throwable $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function bonaeraOutUpdateSendSlack(string $groupNo): void
    {
        $webhookUrl = SlackConstant::WMS_INFO_SLACK();
        $message    = "[WMS 출고 알림]\n";

        $deliveryObj = BonaeraOutDeliveryData::where("group_no", $groupNo)->first();
        if( $deliveryObj !== null ){
            $message .= "배송번호 : {$groupNo}\n";
            $message .= "상태 : " . BonaeraConstant::GROUP_STATUS[$deliveryObj->state] . "\n";
            $message .= "출고번호 : \n";

            $outBaseObjs = BonaeraOutBaseData::with([
                "order.channel_obj"
            ])->where("group_no", $groupNo)->get();
            $idx = 1;
            foreach ($outBaseObjs as $outBaseObj) {
                $channelOrderId  = $outBaseObj->order->channel_obj->channel_order_id ?? "";
                $message        .= $idx . ". " . $outBaseObj->sh_no . " (" . $channelOrderId . ")\n";
                $idx++;
            }

            $message   .= "운송장번호 : {$deliveryObj->invoice}\n";
            $weightObj  = BonaeraOutWeightData::where("group_no", $groupNo)->first();
            if( $weightObj !== null ){
                $message .= "총 배송금액 : " . number_format($weightObj->total_money) . "\n";
            }
            $res = $this->slack->sendMessage($webhookUrl, $message);
            if( $res["isSuccess"] === false ){
                $msg = "error: " . $res["msg"] . " | groupNo: {$groupNo}";
                debug_log($msg, "boneara/bonaeraSlack", "bonaeraOutUpdateSendSlack");
            }
        }
    }

    public function outDetail(string $groupNo): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $res = BonaeraOutBaseData::with([
                "order.product",
                "order.channel_obj",
                "out_options.w_option",
                "logistics_last",
                "out_delivery",
                "out_weight",
                "pay_fail_log",
                "out_extras",
                "out_delivery_extras",
                "out_boxs"
            ])
            ->where("group_no", $groupNo)->first();

            if( $res !== null ){
                $otherObjs = BonaeraOutBaseData::select([
                    "bonaera_out_base_datas.*",
                ])
                ->with(["order.product", "out_options.w_option"])
                ->where("bonaera_out_base_datas.group_no", $res->group_no)
                ->where("bonaera_out_base_datas.id", "!=", $res->id)
                ->get();
                
                $res["otherObjs"] = $otherObjs;
            }
            
            if( $res === null ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_OUT_BASE_DATA"));
            }

            $returnMsg = helpers_success_message($res);
        } catch (Throwable $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function bonaeraDeliveryBundle(int $id, string $changeGroupNo): void
    {
        try {
            $baseObj = BonaeraOutBaseData::where("id", $id)->first();
            if( $baseObj == null ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_OUT_BASE_DATA"));
            }

            $anotherCount = BonaeraOutBaseData::where("group_no", $baseObj->group_no)->where("id", "!=", $baseObj->id)->count();
            if( $anotherCount < 1 ){
                BonaeraOutDeliveryExtraData::where("group_no", $baseObj->group_no)->forceDelete();
            }
            if( $anotherCount < 1 ){
                BonaeraOutDeliveryPayLogData::where("group_no", $baseObj->group_no)->forceDelete();
            }

            BonaeraOutBaseData::where("id", $id)->update([
                "group_no" => $changeGroupNo
            ]);

            $this->bonaeraOutUpdate($id);
        } catch (Throwable $e) {
            $msg = "error: " . $e->getMessage() . " | id: {$id}";
            debug_log($msg, "boneara/bonaeraDeliveryBundle", "bonaeraDeliveryBundle");
        }
    }

    public function bonaeraOutBox(string $groupNo): array
    {
        $returnMsg = helpers_fail_message();

        try {
            $objs = BonaeraOutBoxData::where("group_no", $groupNo)->get();
            
            if( count($objs) < 1 ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_OUT_BOX_DATAS")); 
            }

            $returnMsg = helpers_success_message($objs);
        } catch (Throwable $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}