<?php

namespace App\Http\Controllers\WApp\Order;

use App\Http\Controllers\Controller;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    private Request $request;
    private OrderService $orderService;

    function __construct(Request $request, OrderService $orderService)
    {
        $this->request      = $request;
        $this->orderService = $orderService;
    }

    public function orderList(): View
    {
        $page        = $this->request->post("page", 1);
        $pageSize    = $this->request->post("pageSize", 20);
        $orderStatus = $this->request->get("orderStatus", "");
        $dateCls     = $this->request->get("dateCls", "");
        $startTime   = $this->request->get("startTime", "");
        $endTime     = $this->request->get("endTime", "");

        $pageSize = $pageSize > 20 ? 20 : $pageSize;
        $offset   = ($page - 1) * $pageSize;

        $params = [
            "page"        => $page,
            "pageSize"    => $pageSize,
            "orderStatus" => $orderStatus,
            "dateCls"     => $dateCls,
            "startTime"   => $startTime,
            "endTime"     => $endTime,
        ];
        $result = $this->orderService->orderList($params);

        $viewParams = [
            "datas"               => $result["paginator"],
            "transYCnt"           => $result["transYCnt"],
            "transNCnt"           => $result["transNCnt"],
            "totalCnt"            => $result["totalCnt"],
            "inspectYCnt"         => $result["inspectYCnt"],
            "inspectNCnt"         => $result["inspectNCnt"],
            "imgInspectYCnt"      => $result["imgInspectYCnt"],
            "imgInspectNCnt"      => $result["imgInspectNCnt"],
            "prdInspectYCnt"      => $result["prdInspectYCnt"],
            "prdInspectNCnt"      => $result["prdInspectNCnt"],
            "gosiInspectYCnt"     => $result["gosiInspectYCnt"],
            "gosiInspectNCnt"     => $result["gosiInspectNCnt"],
            "offset"              => (int) $offset,
            "pageSize"            => (int) $pageSize,
        ];

        return view("product.prdList")->with($viewParams);
    }
}
