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
        $page         = $this->request->post("page", 1);
        $pageSize     = $this->request->post("pageSize", 20);
        $orderStatus  = $this->request->get("orderStatus", "");
        $refundStatus = $this->request->get("refundStatus", "");
        $timeCls      = $this->request->get("timeCls", "");
        $startTime    = $this->request->get("startTime", "");
        $endTime      = $this->request->get("endTime", "");

        $pageSize = $pageSize > 20 ? 20 : $pageSize;
        $offset   = ($page - 1) * $pageSize;

        $params = [
            "page"         => $page,
            "pageSize"     => $pageSize,
            "orderStatus"  => $orderStatus,
            "refundStatus" => $refundStatus,
            "timeCls"      => $timeCls,
            "startTime"    => $startTime,
            "endTime"      => $endTime,
        ];
        $result = $this->orderService->orderList($params);

        $viewParams = [
            "paginator"    => $result["data"],
            "offset"       => (int) $offset,
            "pageSize"     => (int) $pageSize,
            "orderStatus"  => $orderStatus,
            "refundStatus" => $refundStatus,
            "timeCls"      => $timeCls,
            "startTime"    => $startTime,
            "endTime"      => $endTime,
        ];
        // dd($viewParams["paginator"]->items()[0]["productItems"]);
        // dd($viewParams["paginator"]->items());
        return view("order.list")->with($viewParams);
    }
}
