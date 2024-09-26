<?php

namespace App\Services\Wms;

use App\Abstracts\WmsAbstract;
use App\Constants\WmsConstant;
use App\Models\HsCodeData;
use Exception;
use SimpleXMLElement;

class WmsW1 extends WmsAbstract
{
    public function __construct()
    {
        parent::__construct();
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

            $builder->orderBy($sortArr[0], $sortArr[1]);

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
}