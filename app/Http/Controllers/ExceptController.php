<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ExceptService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExceptController extends Controller
{
    private Request $request;
    private ExceptService $exceptService;

    function __construct(Request $request, ExceptService $exceptService)
    {
        $this->request       = $request;
        $this->exceptService = $exceptService;
    }

    public function noticeList(): View
    {
        $page        = $this->request->post("page", 1);
        $pageSize    = $this->request->post("pageSize", 50);
        $except_type = $this->request->get("except_type", "");
        $keyword     = $this->request->get("keyword", "");
        $offset      = ($page - 1) * $pageSize;

        $params = [
            "page"        => $page,
            "pageSize"    => $pageSize,
            "except_type" => $except_type,
            "keyword"     => $keyword,
        ];
        $result = $this->exceptService->noticeList($params);

        $viewParams = [
            "except_type" => $except_type,
            "keyword"     => $keyword,
            "datas"       => $result,
            "pageSize"    => $pageSize,
            "offset"      => (int) $offset,
            "totalCnt"    => (int) $result->total(),
        ];
        return view("except.noticeList")->with($viewParams);
    }
}
