<?php

namespace App\Http\Controllers;

use App\Constants\ProductConstant;
use App\Services\EasySellService;
use Illuminate\View\View;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Cast\Array_;

class EasySellController extends Controller
{
    private Request $request;
    private EasySellService $easySellService;

    function __construct(Request $request, EasySellService $easySellService)
    {
        $this->request         = $request;
        $this->easySellService = $easySellService;
    }

    public function getPrdList():View
    {
        $page         = $this->request->get("page", 1);
        $pageSize     = $this->request->get("pageSize", 100);
        $registStatus = $this->request->get("registStatus", "");
        $search_cls   = $this->request->get("search_cls", "offer_id");
        $keyword      = $this->request->get("keyword", "");
        $cate_first   = $this->request->get("cate_first", "");
        $cate_second  = $this->request->get("cate_second", "");
        $cate_third   = $this->request->get("cate_third", "");
        $offset       = ($page - 1) * $pageSize;

        $params = [
            "page"         => $page,
            "pageSize"     => $pageSize,
            "registStatus" => $registStatus,
            "search_cls"   => $search_cls,
            "keyword"      => $keyword,
            "cate_first"   => $cate_first,
            "cate_second"  => $cate_second,
            "cate_third"   => $cate_third
        ];
        $result = $this->easySellService->getPrdList($params);

        $viewParams = [
            "datas"           => $result["paginator"],
            "totalCnt"        => $result["totalCnt"],
            "successCnt"      => $result["successCnt"],
            "failCnt"         => $result["failCnt"],
            "esCateFirstList" => $result["esCateFirstList"],
            "registStatus"    => $registStatus,
            "search_cls"      => $search_cls,
            "keyword"         => $keyword,
            "offset"          => (int) $offset,
            "pageSize"        => (int) $pageSize,
            "firstCateObjs"   => $result["firstCateObjs"],
            "secondCateObjs"  => $result["secondCateObjs"],
            "thirdCateObjs"   => $result["thirdCateObjs"],
            "cate_first"      => $cate_first,
            "cate_second"     => $cate_second,
            "cate_third"      => $cate_third
        ];
        return view("easysell.prdList")->with($viewParams);
    }

    public function categoryManage():View
    {
        $page           = $this->request->post("page", 1);
        $pageSize       = $this->request->post("pageSize", 50);
        $keyword        = $this->request->get("keyword", "");
        $mapping_status = $this->request->get("mapping_status", "");
        $cate_first     = $this->request->get("cate_first", "");
        $cate_second    = $this->request->get("cate_second", "");
        $cate_third     = $this->request->get("cate_third", "");
        $cate_fourth     = $this->request->get("cate_fourth", "");
        $offset         = ($page - 1) * $pageSize;

        $params = [
            "page"           => $page,
            "pageSize"       => $pageSize,
            "keyword"        => $keyword,
            "mapping_status" => $mapping_status,
            "cate_first"     => $cate_first,
            "cate_second"    => $cate_second,
            "cate_third"     => $cate_third,
            "cate_fourth"    => $cate_fourth
        ];
        $result = $this->easySellService->cateList($params);

        $viewParams = [
            "mapping_status"  => $mapping_status,
            "keyword"         => $keyword,
            "offset"          => $offset,
            "datas"           => $result["paginator"],
            "esCateFirstList" => $result["esCateFirstList"],
            "cateFirstList"   => $result["cateFirstList"],
            "cateSecondList"  => $result["cateSecondList"],
            "cateThirdList"   => $result["cateThirdList"],
            "cateFourthList"  => $result["cateFourthList"],
            "cate_first"      => $cate_first,
            "cate_second"     => $cate_second,
            "cate_third"      => $cate_third,
            "cate_fourth"     => $cate_fourth
        ];
        return view("easysell.categoryManage")->with($viewParams);
    }

    public function categoryDepth():array
    {
        $cateType   = $this->request->post("cateType");
        $cateFirst  = $this->request->post("cateFirst");
        $categoryNm = $this->request->post("categoryNm");
        $level      = $this->request->post("level");

        $params = [
            "cateType"   => $cateType,
            "cateFirst"  => $cateFirst,
            "categoryNm" => $categoryNm,
            "level"      => $level,
        ];
        $result = $this->easySellService->categoryDepth($params);
        return $result;
    }

    public function categoryInfo():array
    {
        $categoryCode = $this->request->post("categoryCode");
        $cate_first   = $this->request->post("cate_first", "");
        $cate_second  = $this->request->post("cate_second", "");
        $cate_third   = $this->request->post("cate_third", "");
        $cate_fourth  = $this->request->post("cate_fourth", "");

        $params = [
            "categoryCode" => $categoryCode,
            "cate_first"   => $cate_first,
            "cate_second"  => $cate_second,
            "cate_third"   => $cate_third,
            "cate_fourth"  => $cate_fourth,
        ];
        $result = $this->easySellService->categoryInfo($params);

        return $result;
    }

    public function categoryMapping(){
        $cateId       = $this->request->post("cateId");
        $selectedCate = $this->request->post("selectedCate");

        $params = [
            "cateId"       => $cateId,
            "selectedCate" => $selectedCate,
        ];
        $result = $this->easySellService->categoryMapping($params);

        return $result;
    }
}
