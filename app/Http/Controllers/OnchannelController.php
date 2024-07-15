<?php

namespace App\Http\Controllers;

use App\Constants\OnchannelConstant;
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

    public function getPrdList(int $send_type = OnchannelConstant::PRD_CHANNEL): View
    {
        $page         = $this->request->get("page", 1);
        $pageSize     = $this->request->get("pageSize", 50);
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
            "cate_third"   => $cate_third,
            "send_type"    => $send_type,
        ];
        $result = $this->onchannelService->getPrdList($params);

        $viewParams = [
            "datas"                => $result["paginator"],
            "totalCnt"             => $result["totalCnt"],
            "successCnt"           => $result["successCnt"],
            "errorCnt"             => $result["errorCnt"],
            "registStatus"         => $registStatus,
            "search_cls"           => $search_cls,
            "keyword"              => $keyword,
            "offset"               => (int) $offset,
            "pageSize"             => (int) $pageSize,
            "firstCateObjs"        => $result["firstCateObjs"],
            "secondCateObjs"       => $result["secondCateObjs"],
            "thirdCateObjs"        => $result["thirdCateObjs"],
            "cate_first"           => $cate_first,
            "cate_second"          => $cate_second,
            "cate_third"           => $cate_third,
            "send_type"            => $send_type,
            "channelCateFirstList" => $result["channelCateFirstList"],
        ];
        return view("onchannel.prdList")->with($viewParams);
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
        $cate_fourth    = $this->request->get("cate_fourth", "");
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
        $result = $this->onchannelService->cateList($params);

        $viewParams = [
            "mapping_status"       => $mapping_status,
            "keyword"              => $keyword,
            "offset"               => $offset,
            "datas"                => $result["paginator"],
            "channelCateFirstList" => $result["channelCateFirstList"],
            "cateFirstList"        => $result["cateFirstList"],
            "cateSecondList"       => $result["cateSecondList"],
            "cateThirdList"        => $result["cateThirdList"],
            "cateFourthList"       => $result["cateFourthList"],
            "cate_first"           => $cate_first,
            "cate_second"          => $cate_second,
            "cate_third"           => $cate_third,
            "cate_fourth"          => $cate_fourth
        ];
        return view("onchannel.categoryManage")->with($viewParams);
    }
}
