<?php

namespace App\Http\Controllers;

use App\Constants\ProductConstant;
use App\Services\EasySellService;
use Illuminate\View\View;
use Illuminate\Http\Request;

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
        $page         = $this->request->post("page", 1);
        $pageSize     = $this->request->post("pageSize", 100);
        $registStatus = $this->request->post("registStatus", "");
        $search_cls   = $this->request->get("search_cls", "offer_id");
        $keyword      = $this->request->get("keyword", "");
        $offset       = ($page - 1) * $pageSize;

        $params = [
            "page"         => $page,
            "pageSize"     => $pageSize,
            "registStatus" => $registStatus,
            "search_cls"   => $search_cls,
            "keyword"      => $keyword
        ];
        $result = $this->easySellService->getPrdList($params);

        $viewParams = [
            "datas"        => $result["paginator"],
            "totalCnt"     => $result["totalCnt"],
            "successCnt"   => $result["successCnt"],
            "failCnt"      => $result["failCnt"],
            "registStatus" => $registStatus,
            "search_cls"   => $search_cls,
            "keyword"      => $keyword,
            "offset"       => (int) $offset,
            "pageSize"     => (int) $pageSize
        ];
        return view("easysell.prdList")->with($viewParams);
    }

    public function categoryManage():View
    {
        $page           = $this->request->post("page", 1);
        $pageSize       = $this->request->post("pageSize", 50);
        $keyword        = $this->request->get("keyword", "");
        $mapping_status = $this->request->get("mapping_status", ProductConstant::TRANS_STATUS_Y);
        $cate_first     = $this->request->get("cate_first", "");
        $cate_second    = $this->request->get("cate_second", "");
        $cate_third     = $this->request->get("cate_third", "");
        $cate_third     = $this->request->get("cate_fourth", "");
        $offset         = ($page - 1) * $pageSize;

        $params = [
            "page"           => $page,
            "pageSize"       => $pageSize,
            "keyword"        => $keyword,
            "mapping_status" => $mapping_status,
            "cate_first"     => $cate_first,
            "cate_second"    => $cate_second,
            "cate_third"     => $cate_third
        ];
        $result = $this->easySellService->cateList($params);

        $viewParams = [
            "mapping_status" => $mapping_status,
            "keyword"        => $keyword,
            "offset"         => $offset,
            "datas"          => $result["paginator"],
            "cate_first"     => $cate_first,
            "cate_second"    => $cate_second,
            "cate_third"     => $cate_third
        ];
        return view("easysell.categoryManage")->with($viewParams);
    }
}
