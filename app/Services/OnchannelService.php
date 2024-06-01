<?php

namespace App\Services;

use App\Constants\MallConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Models\Category;
use App\Models\CategoryMapping;
use App\Models\OnchannelProductLog;
use App\Models\OnchCategoryExcelDataCopy2;
use App\Models\ProductData;
use App\Models\WCategory;
use Exception;
use Illuminate\Support\Facades\DB;

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
                "product_datas.*", "b.regist_success", "b.message", "b.prd_code", "b.registed_at", "b.updated_at as b_updated_at"
            ])
            ->with([
                "main_img", "options", "w_mapping"
            ])
            ->leftJoin("onchannel_product_logs as b", "product_datas.offer_id", "=", "b.offer_id")
            ->whereIn("product_datas.w_type", [ WConstant::WAPP_W1, WConstant::WAPP_W2])
            ->where("product_datas.mapping_status", ProductConstant::MAPPING_STATUS_Y)
            ->where("product_datas.status", "!=", ProductConstant::PRD_STATUS_MISS)
            ->orderBy("b.registed_at", "desc")
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

        $countBuilder = ProductData::leftJoin("onchannel_product_logs as b", "product_datas.offer_id", "=", "b.offer_id")
            ->whereIn("product_datas.w_type", [ WConstant::WAPP_W1, WConstant::WAPP_W2])
            ->where("product_datas.mapping_status", ProductConstant::MAPPING_STATUS_Y)
            ->where("product_datas.status", "!=", ProductConstant::PRD_STATUS_MISS);

        $successCnt = (clone $countBuilder)->where("regist_success", MallConstant::REGIST_SUCCESS)->count();

        $failCnt = (clone $countBuilder)->where(function($query){
            $query->where("b.regist_success", MallConstant::REGIST_FAIL)
                ->orWhereNull("b.regist_success");
        })->count();

        $errorCnt = (clone $countBuilder)->where("regist_success", MallConstant::REGIST_ERROR)->count();

        $lists = $prdBuilder->paginate($pageSize)->appends($params);

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
            "a.cate_first", "a.cate_second", "a.cate_third", "a.cate_fourth",
            "a.mapping_code", "b.category_id", "c.mapping_code as channel_mapping_code"
        ])
        ->with(["oc_category"])
        ->from("w_categories as a")
        ->join('category_mappings as b', function($join) {
            $join->on('a.mapping_code', '=', 'b.mapping_code')->where('b.mapping_channel', ProductConstant::MAPPING_WAPP);
        })
        ->leftJoin('category_mappings as c', function($join) {
            $join->on('b.category_id', '=', 'c.category_id')->where('c.mapping_channel', ProductConstant::MAPPING_OC_CHANNEL);
        })
        ->orderBy("a.cate_first", "asc")
        ->orderBy("a.cate_second", "asc")
        ->orderBy("a.cate_third", "asc")
        ->orderBy("a.cate_fourth", "asc")
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

        $cateList      = new WCategory();
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

        $channelCateFirstList = OnchCategoryExcelDataCopy2::orderBy("fir_cate", "asc")->groupBy("fir_cate")->pluck("fir_cate");

        $lists = $categoryBuilder->paginate($pageSize)->appends($params);

        return [
            "channelCateFirstList" => $channelCateFirstList,
            "cateFirstList"        => $cateFirstList,
            "cateSecondList"       => $cateSecondList,
            "cateThirdList"        => $cateThirdList,
            "cateFourthList"       => $cateFourthList,
            "paginator"            => $lists,
        ];
    }

    public function categoryDepth(array $params):array
    {
        $categoryList = [];

        if($params['cateType'] == 'select-opt') {
            $category = new WCategory();
        }else{
            $category = new OnchCategoryExcelDataCopy2();
        }

        $cateListBuilder = $category::where("cate_first",$params['cateFirst']);
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

    public function categoryInfo(array $params) :array
    {
        $categoryCode = $params['categoryCode'];
        $cateNm       = "";
        $cateParams   = [];

        if(isset($categoryCode)){
            $cateObj = WCategory::where("mapping_code", $categoryCode)->first();
            if(isset($cateObj)){
                if(!empty($cateObj->cate_first)){
                    $cateNm = $cateObj->cate_first;
                }
                if(!empty($cateObj->cate_second)){
                    $cateNm .= " > ".$cateObj->cate_second;
                }
                if(!empty($cateObj->cate_third)){
                    $cateNm .= " > ".$cateObj->cate_third;
                }
                if(!empty($cateObj->cate_fourth)){
                    $cateNm .= " > ".$cateObj->cate_fourth;
                }
            }
        }else{
            $cateParams = $params;
        }

        return [
            "cateNm" => $cateNm,
            "cateList" => $this->categoryList($cateParams)
        ];
    }

    public function categoryList(array $params = []){
        $cateBuilder = OnchCategoryExcelDataCopy2::query();
        $cateBuilder->where("cate_first", "");

        if(isset($params['cate_second'])){
            $cateBuilder->where("cate_second",$params['cate_second']);
        }
        if(isset($params['cate_third'])){
            $cateBuilder->where("cate_third",$params['cate_third']);
        }
        if(isset($params['cate_fourth'])){
            $cateBuilder->where("cate_fourth",$params['cate_fourth']);
        }

        if(!empty($params['keyword'])){
            $keyword = $params['keyword'];
            $cateBuilder->where(function($query) use ($keyword) {
                $query->where("cate_first", "like", "%" . $keyword . "%")
                    ->orWhere("cate_second", "like", "%" . $keyword . "%")
                    ->orWhere("cate_third", "like", "%" . $keyword . "%")
                    ->orWhere("cate_fourth", "like", "%" . $keyword . "%");
            });
        }

        return $cateBuilder->get()->toArray();
    }

    public function categoryMapping(array $params):array
    {
        $rtMsg = helpers_fail_message();

        $cateId       = $params["cateId"];
        $selectedCate = $params["selectedCate"];

        try{
            DB::beginTransaction();

            $selCateObj = CategoryMapping::where("mapping_code", $cateId)
                ->where("mapping_channel", ProductConstant::MAPPING_WAPP)
                ->get();

            if(count($selCateObj) == 0){
                throw new Exception("WApp 카테고리 매핑 정보가 없습니다");
            }

            foreach($selCateObj as $cate){
                CategoryMapping::updateOrCreate([
                        "mapping_channel" => ProductConstant::MAPPING_ES_FGN_CHANNEL,
                        "category_id"     => $cate->category_id
                    ], [ "mapping_code" => $selectedCate ]);
            }

            DB::commit();
            $rtMsg = helpers_success_message();
        }catch(Exception $e){
            DB::rollback();

            $rtMsg = helpers_fail_message($e->getMessage());
        }

        return $rtMsg;
    }
}
