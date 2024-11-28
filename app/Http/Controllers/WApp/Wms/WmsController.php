<?php

namespace App\Http\Controllers\WApp\Wms;

use App\Constants\WmsConstant;
use App\Http\Controllers\Controller;
use App\Services\Wms\WmsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WmsController extends Controller
{
    private Request $request;
    private WmsService $wmsService;

    function __construct(Request $request, WmsService $wmsService)
    {
        $this->request      = $request;
        $this->wmsService = $wmsService;
    }

    public function hsCodeList(): View
    {
        $page       = $this->request->get("page", 1);
        $pageSize   = $this->request->get("pageSize", 50);
        $pageSize   = $pageSize > 500 ? 500 : $pageSize;
        $search_cls = $this->request->get("search_cls", WmsConstant::HSCODE_SEARCH_TYPE_KO);
        $keyword    = $this->request->get("keyword", "");
        $sort       = $this->request->get("sort", "property_code_name|asc");

        $pageSize = $pageSize > 500 ? 500 : $pageSize;
        $offset   = ($page - 1) * $pageSize;

        $params = [
            "page"       => $page,
            "pageSize"   => $pageSize,
            "search_cls" => $search_cls,
            "keyword"    => $keyword,
            "sort"       => $sort,
        ];
        $result = $this->wmsService->hsCodeList($params);
        $viewParams = [
            "search_cls" => $search_cls,
            "keyword"    => $keyword,
            "paginator"  => $result["data"],
            "offset"     => (int) $offset,
            "pageSize"   => (int) $pageSize,
        ];
        return view("wms.hsCodeList")->with($viewParams);
    }

    public function inList(): View
    {
        $page       = $this->request->get("page", 1);
        $pageSize   = $this->request->get("pageSize", 50);
        $pageSize   = $pageSize > 500 ? 500 : $pageSize;
        $status     = $this->request->get("status", "");
        $timeCls    = $this->request->get("time_cls", "");
        $startTime  = $this->request->get("start_time", "");
        $endTime    = $this->request->get("end_time", "");
        $search_cls = $this->request->get("search_cls", WmsConstant::IN_SEARCH_TYPE_STOCK_NO);
        $keyword    = $this->request->get("keyword", "");
        $sort       = $this->request->get("sort", "created_at|desc");

        $pageSize = $pageSize > 500 ? 500 : $pageSize;
        $offset   = ($page - 1) * $pageSize;

        $params = [
            "page"       => $page,
            "pageSize"   => $pageSize,
            "status"     => $status,
            "timeCls"    => $timeCls,
            "startTime"  => $startTime,
            "endTime"    => $endTime,
            "search_cls" => $search_cls,
            "keyword"    => $keyword,
            "sort"       => $sort,
        ];
        $result     = $this->wmsService->inList($params);
        $viewParams = [
            "status"     => $status,
            "timeCls"    => $timeCls,
            "startTime"  => $startTime,
            "endTime"    => $endTime,
            "search_cls" => $search_cls,
            "keyword"    => $keyword,
            "paginator"  => $result["data"],
            "offset"     => (int) $offset,
            "pageSize"   => (int) $pageSize,
        ];
        return view("wms.inList")->with($viewParams);
    }

    public function inFailList(): View
    {
        $page       = $this->request->get("page", 1);
        $pageSize   = $this->request->get("pageSize", 50);
        $pageSize   = $pageSize > 500 ? 500 : $pageSize;
        $timeCls    = $this->request->get("time_cls", "");
        $startTime  = $this->request->get("start_time", "");
        $endTime    = $this->request->get("end_time", "");
        $search_cls = $this->request->get("search_cls", WmsConstant::IN_SEARCH_TYPE_ORDER_ID);
        $keyword    = $this->request->get("keyword", "");
        $sort       = $this->request->get("sort", "created_at|desc");

        $pageSize = $pageSize > 500 ? 500 : $pageSize;
        $offset   = ($page - 1) * $pageSize;

        $params = [
            "page"       => $page,
            "pageSize"   => $pageSize,
            "timeCls"    => $timeCls,
            "startTime"  => $startTime,
            "endTime"    => $endTime,
            "search_cls" => $search_cls,
            "keyword"    => $keyword,
            "sort"       => $sort,
        ];
        $result     = $this->wmsService->inFailList($params);
        $viewParams = [
            "timeCls"    => $timeCls,
            "startTime"  => $startTime,
            "endTime"    => $endTime,
            "search_cls" => $search_cls,
            "sort"       => $sort,
            "keyword"    => $keyword,
            "paginator"  => $result["data"],
            "offset"     => (int) $offset,
            "pageSize"   => (int) $pageSize,
        ];
        return view("wms.inFailList")->with($viewParams);
    }

    public function inDetail(string $stockNo): View
    {
        $result = $this->wmsService->inDetail($stockNo);
        return view("wms.inDetail")->with($result);
    }

    public function outSignList(): View
    {
        $page       = $this->request->get("page", 1);
        $pageSize   = $this->request->get("pageSize", 50);
        $pageSize   = $pageSize > 500 ? 500 : $pageSize;
        $status     = $this->request->get("status", "");
        $inStatus   = $this->request->get("in_status", "");
        $timeCls    = $this->request->get("time_cls", "order");
        $startTime  = $this->request->get("start_time", "");
        $endTime    = $this->request->get("end_time", "");
        $search_cls = $this->request->get("search_cls", WmsConstant::OUT_SIGN_SEARCH_TYPE_ORDER_ID);
        $keyword    = $this->request->get("keyword", "");
        $sort       = $this->request->get("sort", "created_at|desc");

        $pageSize = $pageSize > 500 ? 500 : $pageSize;
        $offset   = ($page - 1) * $pageSize;

        $params = [
            "page"       => $page,
            "pageSize"   => $pageSize,
            "status"     => $status,
            "inStatus"   => $inStatus,
            "timeCls"    => $timeCls,
            "startTime"  => $startTime,
            "endTime"    => $endTime,
            "search_cls" => $search_cls,
            "keyword"    => $keyword,
            "sort"       => $sort,
        ];
        $result     = $this->wmsService->outSignList($params);
        $viewParams = [
            "status"     => $status,
            "inStatus"   => $inStatus,
            "timeCls"    => $timeCls,
            "startTime"  => $startTime,
            "endTime"    => $endTime,
            "search_cls" => $search_cls,
            "keyword"    => $keyword,
            "sort"       => $sort,
            "paginator"  => $result["data"],
            "offset"     => (int) $offset,
            "pageSize"   => (int) $pageSize,
        ];
        return view("wms.outSignList")->with($viewParams);
    }

    public function outList(): View
    {
        $page          = $this->request->get("page", 1);
        $pageSize      = $this->request->get("pageSize", 50);
        $pageSize      = $pageSize > 500 ? 500 : $pageSize;
        $status        = $this->request->get("status", "");
        $clearanceType = $this->request->get("clearance_type", "");
        $shippingType  = $this->request->get("shipping_type", "");
        $unipassType   = $this->request->get("unipass_type", "");
        $timeCls       = $this->request->get("time_cls", "order");
        $startTime     = $this->request->get("start_time", "");
        $endTime       = $this->request->get("end_time", "");
        $search_cls    = $this->request->get("search_cls", WmsConstant::HSCODE_SEARCH_TYPE_KO);
        $keyword       = $this->request->get("keyword", "");
        $sort          = $this->request->get("sort", "created_at|desc");

        $pageSize = $pageSize > 500 ? 500 : $pageSize;
        $offset   = ($page - 1) * $pageSize;

        $params = [
            "page"          => $page,
            "pageSize"      => $pageSize,
            "status"        => $status,
            "clearanceType" => $clearanceType,
            "shippingType"  => $shippingType,
            "unipassType"   => $unipassType,
            "timeCls"       => $timeCls,
            "startTime"     => $startTime,
            "endTime"       => $endTime,
            "search_cls"    => $search_cls,
            "keyword"       => $keyword,
            "sort"          => $sort,
        ];
        $result     = $this->wmsService->outList($params);
        $viewParams = [
            "status"        => $status,
            "clearanceType" => $clearanceType,
            "shippingType"  => $shippingType,
            "unipassType"   => $unipassType,
            "timeCls"       => $timeCls,
            "startTime"     => $startTime,
            "endTime"       => $endTime,
            "search_cls"    => $search_cls,
            "keyword"       => $keyword,
            "paginator"     => $result["data"],
            "offset"        => (int) $offset,
            "pageSize"      => (int) $pageSize,
        ];
        return view("wms.outList")->with($viewParams);
    }

    public function outDetail(string $groupNo): View
    {
        $result = $this->wmsService->outDetail($groupNo);
        if( $result["isSuccess"] === false ) abort(404);

        return view("wms.outDetail")->with($result);
    }
}
