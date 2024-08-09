<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Message\WMessageService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    private Request $request;
    private WMessageService $wMessageService;

    function __construct(Request $request, WMessageService $wMessageService)
    {
        $this->request            = $request;
        $this->wMessageService = $wMessageService;
    }

    public function list(): View
    {
        $page         = $this->request->post("page", 1);
        $pageSize     = $this->request->post("pageSize", 50);
        $search_cls   = $this->request->get("search_cls", "order_id");
        $keyword      = $this->request->get("keyword", "");
        $channel      = $this->request->get("channel", "");
        $pubSubIsSend = $this->request->get("pub_sub_is_send", "");
        $code         = $this->request->get("code", []);

        $pageSize = $pageSize > 300 ? 300 : $pageSize;
        $offset   = ($page - 1) * $pageSize;

        $params = [
            'page'         => $page,
            'pageSize'     => $pageSize,
            'search_cls'   => $search_cls,
            'keyword'      => $keyword,
            'code'         => $code,
            'channel'      => $channel,
            'pubSubIsSend' => $pubSubIsSend,
        ];
        $result = $this->wMessageService->list($params);
        // dd($result["data"]->toArray());
        $viewParams = [
            "paginator"    => $result["data"],
            "offset"       => (int) $offset,
            "pageSize"     => (int) $pageSize,
            "search_cls"   => $search_cls,
            "keyword"      => $keyword,
            "channel"      => $channel,
            "pubSubIsSend" => $pubSubIsSend,
            "code"         => implode(",", $code),
        ];

        return view("message.list")->with($viewParams);
    }
}
