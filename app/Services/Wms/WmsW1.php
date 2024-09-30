<?php

namespace App\Services\Wms;

use App\Abstracts\WmsAbstract;
use App\Constants\BonaeraErrorMessageConstant;
use App\Constants\WmsConstant;
use App\Models\BonaeraInBaseData;
use App\Models\BonaeraInProductData;
use App\Models\BonaeraInProductImgData;
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
            debug_log($msg, "boneara/bonaeraInUpdate", "bonaeraInUpdate");
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
}