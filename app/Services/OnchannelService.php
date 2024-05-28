<?php

namespace App\Services;

use App\Constants\MallConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Models\Category;
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
        $cate_first   = "";
        $cate_second  = "";
        $cate_third   = "";

        if( isset($params["cate_first"]) ){
            $cate_first = $params["cate_first"];
        }
        if( isset($params["cate_second"]) ){
            $cate_second = $params["cate_second"];
        }
        if( isset($params["cate_third"]) ){
            $cate_third = $params["cate_third"];
        }
        $firstCateObjs  = [];
        $secondCateObjs = [];
        $thirdCateObjs  = [];
        if( $cate_first == "" ){
            $firstCateObjs = Category::where("parent_cate_id", 0)->orderBy("category_name", "asc")->get();
        }else {
            if( $cate_first && $cate_second ){
                $firstCateObjs  = Category::where("parent_cate_id", 0)->orderBy("category_name", "asc")->get();
                $secondCateObjs = Category::where("parent_cate_id", $cate_first)->orderBy("category_name", "asc")->get();
                $thirdCateObjs  = Category::where("parent_cate_id", $cate_second)->orderBy("category_name", "asc")->get();
            } else if( $cate_first ){
                $firstCateObjs  = Category::where("parent_cate_id", 0)->orderBy("category_name", "asc")->get();
                $secondCateObjs = Category::where("parent_cate_id", $cate_first)->orderBy("category_name", "asc")->get();
            }
        }

        $prdBuilder = ProductData::select([
                "product_datas.*", "b.regist_success", "b.message", "b.prd_code", "b.registed_at"
            ])
            ->with([
                "main_img", "options", "w_mapping"
            ])
            ->leftJoin("onchannel_product_logs as b", "product_datas.offer_id", "=", "b.offer_id")
            ->whereIn("product_datas.w_type", [ WConstant::WAPP_W1, WConstant::WAPP_W2])
            ->where("product_datas.mapping_status", ProductConstant::MAPPING_STATUS_Y)
            ->where("product_datas.status", "!=", ProductConstant::PRD_STATUS_MISS)
            ->orderBy("b.registed_at", "desc")
            ->orderBy("product_datas.updated_at", "desc");

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
            } else if($registStatus == MallConstant::UNREGIST){
                $prdBuilder->where(function($query){
                    $query->where("b.regist_success", MallConstant::REGIST_FAIL)
                        ->orWhereNull("b.regist_success");
                });
            } else if($registStatus == MallConstant::REGIST_ERROR){
                $prdBuilder->where(function($query){
                    $query->where("b.regist_success", MallConstant::REGIST_ERROR);
                });
            }
        }

        if( !empty($cate_third) && !empty($cate_second) && !empty($cate_first) ){
            $prdBuilder->where("product_datas.category_id", $cate_third);
        } else if( !empty($cate_second) && !empty($cate_first) ){
            $childCates = Category::where("parent_cate_id", $cate_second)->pluck("category_id");
            if( !empty($childCates) ){
                $prdBuilder->where(function ($query) use ($childCates, $cate_second){
                    $query->whereIn("product_datas.category_id", $childCates)
                    ->orWhere("product_datas.category_id", $cate_second);
                });
            }
        } else if( !empty($cate_first) ){
            $secondChildCates = Category::where("parent_cate_id", $cate_first)->pluck("category_id");
            $thirdChildCates  = Category::whereIn("parent_cate_id", $secondChildCates)->pluck("category_id");
            $allChildCates    = $secondChildCates->merge($thirdChildCates);
            if( !empty($allChildCates) ){
                $prdBuilder->where(function ($query) use ($allChildCates, $cate_first){
                    $query->whereIn("product_datas.category_id", $allChildCates)
                    ->orWhere("product_datas.category_id", $cate_first);
                });
            }
        }

        $successCnt = OnchannelProductLog::where("regist_success", MallConstant::REGIST_SUCCESS)->count();

        $failBuilder = ProductData::leftJoin("onchannel_product_logs as b", "product_datas.offer_id", "=", "b.offer_id")
        ->whereIn("product_datas.w_type", [ WConstant::WAPP_W1, WConstant::WAPP_W2])
        ->where("product_datas.mapping_status", ProductConstant::MAPPING_STATUS_Y)
        ->where("product_datas.status", "!=", ProductConstant::PRD_STATUS_MISS);
        $failCnt = $failBuilder->where(function($query){
            $query->where("b.regist_success", MallConstant::REGIST_FAIL)
                ->orWhereNull("b.regist_success");
        })->count();

        $errorCnt   = OnchannelProductLog::where("regist_success", MallConstant::REGIST_ERROR)->count();
        $lists      = $prdBuilder->paginate($pageSize)->appends($params);

        return [
            "paginator"      => $lists,
            "totalCnt"       => $totalCnt,
            "successCnt"     => $successCnt,
            "failCnt"        => $failCnt,
            "errorCnt"       => $errorCnt,
            "firstCateObjs"  => $firstCateObjs,
            "secondCateObjs" => $secondCateObjs,
            "thirdCateObjs"  => $thirdCateObjs,
        ];
    }
}
