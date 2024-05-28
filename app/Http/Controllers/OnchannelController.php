<?php

namespace App\Http\Controllers;

use App\Services\OnchannelService;
use Illuminate\View\View;
use Illuminate\Http\Request;

class OnchannelController extends Controller
{
    private Request $request;
    private OnchannelService $onchannelService;

    function __construct(Request $request, OnchannelService $onchannelService)
    {
        $this->request         = $request;
        $this->onchannelService = $onchannelService;
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
        $result = $this->onchannelService->getPrdList($params);

        $viewParams = [
            "datas"          => $result["paginator"],
            "totalCnt"       => $result["totalCnt"],
            "successCnt"     => $result["successCnt"],
            "failCnt"        => $result["failCnt"],
            "errorCnt"       => $result["errorCnt"],
            "registStatus"   => $registStatus,
            "search_cls"     => $search_cls,
            "keyword"        => $keyword,
            "offset"         => (int) $offset,
            "pageSize"       => (int) $pageSize,
            "firstCateObjs"  => $result["firstCateObjs"],
            "secondCateObjs" => $result["secondCateObjs"],
            "thirdCateObjs"  => $result["thirdCateObjs"],
            "cate_first"     => $cate_first,
            "cate_second"    => $cate_second,
            "cate_third"     => $cate_third
        ];
        return view("onchannel.prdList")->with($viewParams);
    }
}
