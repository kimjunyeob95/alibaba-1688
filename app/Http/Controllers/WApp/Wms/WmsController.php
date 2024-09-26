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
}
