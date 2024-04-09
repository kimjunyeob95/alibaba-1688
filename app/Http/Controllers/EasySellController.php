<?php

namespace App\Http\Controllers;

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
}
