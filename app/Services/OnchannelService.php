<?php

namespace App\Services;

use App\Constants\MallConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Models\OnchannelProductLog;
use App\Models\ProductData;

class OnchannelService
{
    public function getPrdList($params) :array
    {
        $pageSize     = $params["pageSize"];
        $registStatus = $params["registStatus"];
        $search_cls   = $params["search_cls"];
        $keyword      = $params["keyword"];

        $prdBuilder = ProductData::select(["product_datas.*", "b.regist_success", "b.message", "b.prd_code"])
            ->with([
                "main_img", "options", "w_mapping"
            ])
            ->leftJoin("onchannel_product_logs as b", "product_datas.offer_id", "=", "b.offer_id")
            ->whereIn("product_datas.w_type", [ WConstant::WAPP_W1, WConstant::WAPP_W2])
            ->where("product_datas.mapping_status", ProductConstant::MAPPING_STATUS_Y)
            ->where("product_datas.status", "!=", ProductConstant::PRD_STATUS_MISS)
            ->orderBy("b.updated_at", "desc");

        $totalCnt = ProductData::where("mapping_status", ProductConstant::MAPPING_STATUS_Y)
        ->where("product_datas.status", "!=", ProductConstant::PRD_STATUS_MISS)->count();

        if(isset($search_cls) && !empty($keyword)){
            if($search_cls == "prd_name_kr"){
                $prdBuilder->where("product_datas.prd_name_kr", "like", "%{$keyword}%");
            }else{
                $keyword = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
                $keyword = explode(",", $keyword);
                // 각 배열 요소의 앞뒤 공백 제거
                $keyword = array_map('trim', $keyword);
                // 빈 값을 제거
                $keyword = array_filter($keyword);
                // 중복 제거
                $keyword = array_unique($keyword);

                if($search_cls == "offer_id"){
                    $prdBuilder->whereIn("product_datas.offer_id", $keyword);
                }else if($search_cls == "prd_code"){
                    $prdBuilder->whereIn("b.prd_code", $keyword);
                }
            }
        }
        if(!empty($registStatus)){
            if($registStatus == MallConstant::REGISTED){
                $prdBuilder->where("b.regist_success", MallConstant::REGIST_SUCCESS);
            } else if($registStatus== MallConstant::UNREGIST){
                $prdBuilder->where(function($query){
                    $query->where("b.regist_success", "!=", MallConstant::REGIST_SUCCESS)
                        ->orWhereNull("b.regist_success");
                });
            }
        }
        $successCnt = OnchannelProductLog::where("regist_success", MallConstant::REGIST_SUCCESS)->count();
        $failCnt    = $totalCnt - $successCnt;
        $lists      = $prdBuilder->paginate($pageSize)->appends($params);
        // dd($lists->toArray());
        return [
            "paginator"  => $lists,
            "totalCnt"   => $totalCnt,
            "successCnt" => $successCnt,
            "failCnt"    => $failCnt,
        ];
    }
}
