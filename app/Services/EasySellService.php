<?php

namespace App\Services;

use App\Constants\EasySellConstant;
use App\Constants\MallConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Models\Category;
use App\Models\EasysellProductLog;
use App\Models\ProductData;
use App\Models\SellerhubCategory;
use App\Models\WCategory;

class EasySellService
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
                "product_datas.*", "product_datas.offer_id","product_datas.prd_name_kr","epl.itemno","epl.regist_success","product_datas.category_id",
                "epl.registed_at","pwd.weight_type", "pwd.weight", "pwd.delivery_price"
            ])
            ->with(["main_img", "options", "es_mapping", "es_fgn_mapping", "w_mapping", "w_mapping.w_cate_name"])
            ->join("easysell_product_logs as epl","product_datas.offer_id","=","epl.offer_id")
            ->leftJoin('product_weight_datas as pwd', function ($join) {
                $join->on('product_datas.offer_id', '=', 'pwd.offer_id');
            })
            ->orderBy("product_datas.created_at", "desc");

        $totalCnt = $prdBuilder->count();

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

        $successCnt = EasysellProductLog::where("regist_success",MallConstant::REGIST_SUCCESS)->count();
        $lists      = $prdBuilder->paginate($pageSize)->appends($params);
        $failCnt    = $totalCnt - $successCnt;

        $channelCateFirstList = SellerhubCategory::where("cate_first", EasySellConstant::DEFAULT_CATEGORY)->pluck("cate_first")->unique()->filter();
        $builder              = SellerhubCategory::query();
        $builder->where("cate_first", EasySellConstant::DEFAULT_CATEGORY);
        $channelCateList = $builder->get();

        return [
            "channelCateFirstList" => $channelCateFirstList,
            "channelCateList"      => $channelCateList,
            "paginator"            => $lists,
            "totalCnt"             => $totalCnt,
            "successCnt"           => $successCnt,
            "failCnt"              => $failCnt,
            "firstCateObjs"        => $firstCateObjs,
            "secondCateObjs"       => $secondCateObjs,
            "thirdCateObjs"        => $thirdCateObjs,
        ];
    }

    public function cateList($params) :array
    {
        $pageSize       = $params["pageSize"];
        $mapping_status = $params["mapping_status"];
        $keyword        = $params["keyword"];
        $cate_first     = $params["cate_first"];
        $cate_second    = $params["cate_second"];
        $cate_third     = $params["cate_third"];
        $cate_fourth    = $params["cate_fourth"];

        $cateFirstList  = [];
        $cateSecondList = [];
        $cateThirdList  = [];
        $cateFourthList = [];

        $categoryBuilder = WCategory::select([
            "a.cate_first","a.cate_second","a.cate_third","a.cate_fourth",
            "a.mapping_code","b.category_id", "c.mapping_code as es_mapping_code"
        ])
        ->from("w_categories as a")
        ->with(["es_category"])
        ->join('category_mappings as b', function($join) {
            $join->on('a.mapping_code', '=', 'b.mapping_code')
                ->where('b.mapping_channel', ProductConstant::MAPPING_WAPP);
        })
        ->leftJoin('category_mappings as c', function($join) {
            $join->on('b.category_id', '=', 'c.category_id')
                ->where('c.mapping_channel', ProductConstant::MAPPING_ES_FGN_CHANNEL);
        })
        ->groupBy("a.mapping_code");

        if(!empty($mapping_status)){
            if($mapping_status == "Y"){
                $categoryBuilder->whereNotNull("c.mapping_code");
            }else if($mapping_status == "N"){
                $categoryBuilder->where(function($query){
                    $query->whereNull("c.mapping_code");
                });
            }
        }

        if(!empty($keyword)){
            $categoryBuilder->where(function($query) use ($keyword) {
                $query->where("a.cate_first", "like", "%" . $keyword . "%")
                    ->orWhere("a.cate_second", "like", "%" . $keyword . "%")
                    ->orWhere("a.cate_third", "like", "%" . $keyword . "%")
                    ->orWhere("a.cate_fourth", "like", "%" . $keyword . "%");
            });
        }

        $cateList      = new WCategory;
        $cateFirstList = $cateList->orderBy("cate_first", "asc")->groupBy("cate_first")->pluck("cate_first");
        if(!empty($cate_first)){
            $cateSecondList = $cateList->where("cate_first", $cate_first)->orderBy("cate_second", "asc")->groupBy("cate_second")->pluck("cate_second");
            $categoryBuilder->where("a.cate_first", $cate_first);
        }
        if(!empty($cate_second)){
            $cateThirdList = $cateList->where("cate_second",$cate_second)->orderBy("cate_third", "asc")->groupBy("cate_third")->pluck("cate_third");
            $categoryBuilder->where("a.cate_second", $cate_second);
        }
        if(!empty($cate_third)){
            $cateFourthList = $cateList->where("cate_third",$cate_third)->orderBy("cate_fourth", "asc")->groupBy("cate_fourth")->pluck("cate_fourth");
            $categoryBuilder->where("a.cate_third", $cate_third);
        }
        if(!empty($cate_fourth)){
            $categoryBuilder->where("a.cate_fourth", $cate_fourth);
        }

        $channelCateFirstList = SellerhubCategory::where("cate_first", EasySellConstant::DEFAULT_CATEGORY)->pluck("cate_first")->unique()->filter();
        $builder = SellerhubCategory::query();
        $builder->where("cate_first", EasySellConstant::DEFAULT_CATEGORY);
        $channelCateList = $builder->get();

        $lists = $categoryBuilder->paginate($pageSize)->appends($params);

        return [
            "channelCateFirstList" => $channelCateFirstList,
            "channelCateList"      => $channelCateList,
            "cateFirstList"        => $cateFirstList,
            "cateSecondList"       => $cateSecondList,
            "cateThirdList"        => $cateThirdList,
            "cateFourthList"       => $cateFourthList,
            "paginator"            => $lists,
        ];
    }
}
