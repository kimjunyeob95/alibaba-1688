<?php

namespace App\Http\Controllers;

use App\Constants\HttpConstant;
use App\Http\Controllers\Controller;
use App\Services\GenuioService;
use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;

class GenuioController extends Controller
{
    private Request $request;
    private GenuioService $genuioService;

    function __construct(Request $request, GenuioService $genuioService)
    {
        $this->request       = $request;
        $this->genuioService = $genuioService;
    }

    public function wappQueueList(): View
    {
        try {
            $page            = $this->request->get("page", 1);
            $pageSize        = $this->request->get("pageSize", 1);
            $send_type       = $this->request->get("send_type", "");
            $callback_status = $this->request->get("callback_status", "");
            $search_cls      = $this->request->get("search_cls", "offer_id");
            $keyword         = $this->request->get("keyword", "");
            $offset          = ($page - 1) * $pageSize;

            $params = [
                "page"            => $page,
                "pageSize"        => $pageSize,
                "send_type"       => $send_type,
                "callback_status" => $callback_status,
                "search_cls"      => $search_cls,
                "keyword"         => $keyword,
            ];

            $result = $this->genuioService->wappQueueList($params);

            $viewParams = [
                "datas"           => $result["paginator"],
                "requestCnt"      => $result["requestCnt"],
                "responseCnt"     => $result["responseCnt"],
                "pageSize"        => $pageSize,
                "offset"           => $offset,
                "send_type"       => $send_type,
                "callback_status" => $callback_status,
                "search_cls"      => $search_cls,
                "keyword"         => $keyword,
            ];
            return view("sai.queue.wappList")->with($viewParams);
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }
}
