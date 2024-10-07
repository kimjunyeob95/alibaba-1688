<?php

namespace App\Services\Wms;

use App\Abstracts\WmsAbstract;
use App\Constants\BonaeraConstant;
use App\Constants\BonaeraErrorMessageConstant;
use App\Constants\WmsConstant;
use App\Models\BonaeraInBaseData;
use App\Models\BonaeraInProductData;
use App\Models\BonaeraInProductImgData;
use App\Models\BonaeraOutBaseData;
use App\Models\BonaeraOutDeliveryData;
use App\Models\BonaeraOutWeightData;
use App\Models\HsCodeData;
use App\Packages\Bonaera;
use Carbon\Carbon;
use Exception;
use SimpleXMLElement;

class WmsW1 extends WmsAbstract
{
    public function __construct(Bonaera $bonaera)
    {
        parent::__construct($bonaera);
    }

    public function hsCodeList(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
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
                } else if( $search_cls == WmsConstant::HSCODE_SEARCH_TYPE_HSCODE ){
                    $keyword = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
                    $keyword = explode(",", $keyword);
                    // 각 배열 요소의 앞뒤 공백 제거
                    $keyword = array_map('trim', $keyword);
                    // 빈 값을 제거
                    $keyword = array_filter($keyword);
                    // 중복 제거
                    $keyword = array_unique($keyword);
                    $builder->whereIn("hs_code", $keyword);
                }
            }

            $builder->orderByRaw("CASE WHEN {$sortArr[0]} = '' OR {$sortArr[0]} IS NULL THEN 1 ELSE 0 END, {$sortArr[0]} {$sortArr[1]}");

            $lists = $builder->paginate($pageSize)->appends($params);

            $returnMsg = helpers_success_message($lists);
        } catch (Exception $e) {
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
            ->with(["product", "logistics_last", "in_options"])
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function bonaeraInUpdate(int $id): void
    {
        try {
            $inBaseObj = BonaeraInBaseData::where("id", $id)->first();
            if( $inBaseObj == null ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_BASE_DATA"));
            }

            $res = $this->bonaera->getStockList($inBaseObj->stock_no);

            if( $res["isSuccess"] === true && isset($res["data"]["appCode"]) && isset($res["data"]["appitemList"]) ) {
                $stockCode       = $res["data"]["appCode"];
                $lastCompletedAt = null;
                foreach ($res["data"]["appitemList"] as $item) {
                    $currentDate = Carbon::parse($item["itemIndate"]);
    
                    if ($lastCompletedAt === null || $currentDate->gt($lastCompletedAt)) {
                        $lastCompletedAt = $currentDate;
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

                if( $lastCompletedAt !== null ){
                    $inBaseObj->update([
                        "completed_at" => $lastCompletedAt
                    ]);
                }
            }

        } catch (Exception $e) {
            $msg = "error: " . $e->getMessage();
            debug_log($msg, "boneara/bonaeraUpdate", "bonaeraInUpdate");
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
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function outList(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $pageSize      = $params["pageSize"];
            $status        = $params["status"];
            $clearanceType = $params["clearanceType"];
            $shippingType  = $params["shippingType"];
            $timeCls       = $params["timeCls"];
            $startTime     = $params["startTime"];
            $endTime       = $params["endTime"];
            $search_cls    = $params["search_cls"];
            $keyword       = $params["keyword"];
            $sortArr       = explode("|", $params["sort"]);

            $builder = BonaeraOutBaseData::select([
                "bonaera_out_base_datas.*",
                "bodd.state",
                "bodd.invoice",
                "bodd.receiver_name",
                "bodd.personal_num",
                "bodd.unipass_reason",
                "ocd.clearance_type",
                "ocd.shipping_type",
            ])
            ->with(["order.product", "logistics_last", "out_options.w_option"])
            ->leftJoin("bonaera_out_delivery_datas as bodd", "bonaera_out_base_datas.group_no", "=", "bodd.group_no")
            ->leftJoin("order_channel_datas as ocd", "bonaera_out_base_datas.order_id", "=", "ocd.order_id")
            ->groupBy("bonaera_out_base_datas.group_no");

            if( !empty($status) ){
                if( $status == BonaeraConstant::GROUP_STATUS_300 ){
                    $builder->whereIn("bodd.status", BonaeraConstant::OUT_PEKI_STATUS);
                } else {
                    $builder->where("bodd.status", $status);
                }
            }
            if( !empty($clearanceType) ){
                $builder->where("ocd.clearance_type", $clearanceType);
            }
            if( !empty($shippingType) ){
                $builder->where("ocd.shipping_type", $shippingType);
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
                }
            }

            $builder->orderBy("bonaera_out_base_datas." . $sortArr[0], $sortArr[1]);

            $lists = $builder->paginate($pageSize)->appends($params);

            foreach ($lists as &$data) {
                $otherObjs = BonaeraOutBaseData::select([
                    "bonaera_out_base_datas.*",
                ])
                ->with(["order.product", "out_options.w_option"])
                ->where("bonaera_out_base_datas.group_no", $data->group_no)
                ->where("bonaera_out_base_datas.id", "!=", $data->id)
                ->get();
                
                $data["otherObjs"] = $otherObjs;
            }

            $returnMsg = helpers_success_message($lists);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }
        // dd($returnMsg["data"]->toArray()["data"]);
        return $returnMsg;
    }

    public function bonaeraOutUpdate(int $id): void
    {
        try {
            $baseObj = BonaeraOutBaseData::where("id", $id)->first();
            if( $baseObj == null ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_BASE_DATA"));
            }

            $groupNo = $baseObj->group_no;
            $res     = $this->bonaera->getApplicationList($groupNo);

            if( $res["isSuccess"] === true && isset($res["data"]["data"]["grCode"]) && isset($res["data"]["data"]["ReciverInfo"][0]) ) {
                $res         = $res["data"]["data"];
                $reciverInfo = $res["ReciverInfo"][0];

                BonaeraOutDeliveryData::updateOrCreate(
                    [
                        "group_no" => $groupNo,
                    ],
                    [
                        "invoice"        => $res["invoice"] ?? "",
                        "state"          => $res["state"],
                        "outday"         => $res["outday"] ?? null,
                        "receiver_name"  => $reciverInfo["receiverName"],
                        "zip_code"       => $reciverInfo["zipCode"],
                        "addr1"          => $reciverInfo["addr1"],
                        "addr2"          => $reciverInfo["addr2"],
                        "receiver_phone" => $reciverInfo["receiverPhone"],
                        "personal_type"  => $reciverInfo["personalType"],
                        "personal_num"   => $reciverInfo["personalNum"],
                        "unipass_result" => $reciverInfo["unipassResult"],
                        "unipass_reason" => $reciverInfo["unipassReason"],
                        "ship_memo"      => $reciverInfo["shipMemo"],
                    ]
                );

                if( isset($res["weightList"][0]) ){
                    $weight = $res["weightList"][0];

                    BonaeraOutWeightData::updateOrCreate(
                        [
                            "group_no" => $groupNo,
                        ],
                        [
                            "box_cnt"          => $weight["boxCnt"] ?? 0,
                            "real_weight"      => $weight["realWeight"] ?? 0,
                            "width"            => $weight["width"] ?? 0,
                            "length"           => $weight["length"] ?? 0,
                            "height"           => $weight["height"] ?? 0,
                            "weight"           => $weight["weight"] ?? 0,
                            "ship_money"       => $weight["shipMoney"] ?? 0,
                            "weight_fee"       => $weight["weightFee"] ?? 0,
                            "volume_fee"       => $weight["volumeFee"] ?? 0,
                            "svc_money1"       => $weight["svcMoney1"] ?? 0,
                            "svc_money2"       => $weight["svcMoney2"] ?? 0,
                            "plus_money"       => $weight["plusMoney"] ?? 0,
                            "plus_money_memo"  => $weight["plusMoneyMemo"] ?? "",
                            "minus_money"      => $weight["minusMoney"] ?? 0,
                            "minus_money_memo" => $weight["minusMoneyMemo"] ?? "",
                            "commission"       => $weight["commission"] ?? 0,
                            "islands"          => $weight["islands"] ?? 0,
                            "total_money"      => $weight["totalMoney"] ?? 0,
                        ]
                    );
                }
            }

        } catch (Exception $e) {
            $msg = "error: " . $e->getMessage();
            debug_log($msg, "boneara/bonaeraUpdate", "bonaeraOutUpdate");
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

            $returnMsg = helpers_success_message($res);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}