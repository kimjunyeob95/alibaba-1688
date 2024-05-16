<?php

namespace App\Services;

use App\Constants\MallConstant;
use App\Constants\ProductConstant;
use App\Models\CategoryMapping;
use App\Models\EasysellProductLog;
use App\Models\ProductData;
use App\Models\WCategory;

class EasySellService
{
    public function getPrdList($params) :array
    {
        $pageSize     = $params["pageSize"];
        $registStatus = $params["registStatus"];
        $search_cls   = $params["search_cls"];
        $keyword      = $params["keyword"];

        $prdBuilder = ProductData::select(["product_datas.offer_id","product_datas.prd_name_kr","epl.itemno","epl.regist_success","product_datas.category_id"])
        ->with(["main_img", "options", "es_mapping", "es_fgn_mapping"])
        ->leftJoin("easysell_product_logs as epl","product_datas.offer_id","=","epl.offer_id")
        ->where("product_datas.trans_status", ProductConstant::TRANS_STATUS_Y)
        ->where("product_datas.mapping_status", ProductConstant::MAPPING_STATUS_Y)
        ->whereNull("product_datas.deleted_at")->orderBy("product_datas.created_at", "desc");
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

    public function cateList($params) :array
    {
        $pageSize       = $params["pageSize"];
        $mapping_status = $params["mapping_status"] ?? "";
        $keyword        = $params["keyword"];

        $cate_first     = $params["cate_first"];
        $cate_second    = $params["cate_second"];
        $cate_third     = $params["cate_third"];
        $cate_fourth    = $params["cate_fourth"];


        $cateFirstList  = [];
        $cateSecondList = [];
        $cateThirdList  = [];
        $cateFourthList = [];

        $categoryBuilder = WCategory::select(["a.cate_first","a.cate_second","a.cate_third","a.cate_fourth","a.mapping_code","b.category_id","c.mapping_code as es_mapping_code"])
            ->from("w_categories as a")
            ->with(["es_category"])
            ->leftJoin('category_mappings as b', function($join) {
                $join->on('a.mapping_code', '=', 'b.mapping_code')
                    ->where('b.mapping_channel', ProductConstant::MAPPING_WAPP);
            })
            ->leftJoin('category_mappings as c', function($join) {
                $join->on('b.category_id', '=', 'c.category_id')
                    ->where('c.mapping_channel', ProductConstant::MAPPING_ES_CHANNEL);
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

        $cateList  = new WCategory;
        $cateFirstList  = $cateList->pluck("cate_first")->unique()->filter();
        if(!empty($cate_first)){
            $cateSecondList = $cateList->where("cate_first",$cate_first)->pluck("cate_second")->unique()->filter();
            $categoryBuilder->where("a.cate_first", $cate_first);
        }
        if(!empty($cate_second)){
            $cateThirdList = $cateList->where("cate_second",$cate_second)->pluck("cate_third")->unique()->filter();
            $categoryBuilder->where("a.cate_second", $cate_second);
        }
        if(!empty($cate_third)){
            $cateFourthList = $cateList->where("cate_third",$cate_third)->pluck("cate_fourth")->unique()->filter();
            $categoryBuilder->where("a.cate_third", $cate_third);
        }
        if(!empty($cate_fourth)){
            $categoryBuilder->where("a.cate_fourth", $cate_fourth);
        }

        $lists = $categoryBuilder->paginate($pageSize)->appends($params);

        return [
            "cateFirstList"  => $cateFirstList,
            "cateSecondList" => $cateSecondList,
            "cateThirdList"  => $cateThirdList,
            "cateFourthList" => $cateFourthList,
            "paginator"      => $lists,
        ];
    }

    public function categoryDepth(array $params){
        $categoryList = [];

        $cateListBuilder = WCategory::where("cate_first",$params['cateFirst']);
        if(isset($params['level'])){
            if($params['level'] == 1){
                $categoryList = $cateListBuilder->groupBy("cate_second")
                ->pluck("cate_second")
                ->filter();
            }else if($params['level'] == 2){
                $categoryList = $cateListBuilder->where("cate_second", $params['categoryNm'])
                    ->groupBy("cate_third")
                    ->pluck("cate_third")
                    ->filter();
            }else if($params['level'] == 3){
                $categoryList =  $cateListBuilder->where("cate_third", $params['categoryNm'])
                    ->groupBy("cate_fourth")
                    ->pluck("cate_fourth")
                    ->filter();
            }
        }

        return [
            "categoryList" => $categoryList
        ];
    }
}
