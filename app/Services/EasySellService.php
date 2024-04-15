<?php

namespace App\Services;

use App\Constants\MallConstant;
use App\Constants\ProductConstant;
use App\Models\EasysellProductLog;
use App\Models\ProductData;

class EasySellService
{
    public function getPrdList($params) :array
    {
        $pageSize     = $params["pageSize"];
        $registStatus = $params["registStatus"];
        $search_cls   = $params["search_cls"];
        $keyword      = $params["keyword"];

        $prdBuilder = ProductData::select(["product_datas.offer_id","product_datas.prd_name_trans","epl.itemno","epl.regist_success"])
        ->with(["main_img", "options"])
        ->leftJoin("easysell_product_logs as epl","product_datas.offer_id","=","epl.offer_id")
        ->where("product_datas.trans_status", ProductConstant::TRANS_STATUS_Y)
        ->where("product_datas.mapping_status", ProductConstant::MAPPING_STATUS_Y)
        ->whereNull("product_datas.deleted_at")->orderBy("product_datas.created_at", "desc");
        $totalCnt = $prdBuilder->count();

        if(isset($search_cls) && !empty($keyword)){
            if($search_cls == "prd_name_trans"){
                $prdBuilder->where("product_datas.prd_name_trans", "like", "%{$keyword}%");
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
                }else if($search_cls == "itemno"){
                    $prdBuilder->whereIn("epl.itemno", $keyword);
                }
            }
        }
        if(!empty($registStatus)){
            if($registStatus == MallConstant::REGISTED){
                $prdBuilder->where("epl.regist_success", MallConstant::REGIST_SUCCESS);
            } else if($registStatus== MallConstant::UNREGIST){
                $prdBuilder->where(function($query){
                    $query->where("epl.regist_success", "!=", MallConstant::REGIST_SUCCESS)
                        ->orWhereNull("epl.regist_success");
                });
            }
        }
        $successCnt = EasysellProductLog::where("regist_success",MallConstant::REGIST_SUCCESS)->count();
        $failCnt    = $totalCnt - $successCnt;
        $lists      = $prdBuilder->paginate($pageSize)->appends($params);

        return [
            "paginator"  => $lists,
            "totalCnt"   => $totalCnt,
            "successCnt" => $successCnt,
            "failCnt"    => $failCnt,
        ];
    }
}
