<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ForbiddenWordService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForbiddenWordController extends Controller
{
    private Request $request;
    private ForbiddenWordService $forbiddenWordService;

    function __construct(Request $request, ForbiddenWordService $forbiddenWordService)
    {
        $this->request              = $request;
        $this->forbiddenWordService = $forbiddenWordService;
    }

    public function list(): View
    {
        $page         = $this->request->post("page", 1);
        $pageSize     = $this->request->post("pageSize", 50);
        $keyword_type = $this->request->get("keyword_type", "");
        $keyword      = $this->request->get("keyword", "");
        $offset       = ($page - 1) * $pageSize;

        $params = [
            "page"         => $page,
            "pageSize"     => $pageSize,
            "keyword_type" => $keyword_type,
            "keyword"      => $keyword,
        ];
        $result = $this->forbiddenWordService->list($params);

        $viewParams = [
            "keyword_type" => $keyword_type,
            "keyword"      => $keyword,
            "datas"        => $result,
            "pageSize"     => $pageSize,
            "offset"       => (int) $offset,
            "totalCnt"     => (int) $result->total(),
        ];
        return view("forbiddenWord.list")->with($viewParams);
    }

    public function noticeList(): View
    {
        $page         = $this->request->post("page", 1);
        $pageSize     = $this->request->post("pageSize", 50);
        $keyword_type = $this->request->get("keyword_type", "");
        $keyword      = $this->request->get("keyword", "");
        $offset       = ($page - 1) * $pageSize;

        $params = [
            "page"         => $page,
            "pageSize"     => $pageSize,
            "keyword_type" => $keyword_type,
            "keyword"      => $keyword,
        ];
        $result = $this->forbiddenWordService->noticeList($params);

        $viewParams = [
            "keyword_type" => $keyword_type,
            "keyword"      => $keyword,
            "datas"        => $result,
            "pageSize"     => $pageSize,
            "offset"       => (int) $offset,
            "totalCnt"     => (int) $result->total(),
        ];
        return view("forbiddenWord.noticeList")->with($viewParams);
    }
}
