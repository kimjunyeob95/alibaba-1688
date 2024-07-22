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
        $page           = $this->request->post("page", 1);
        $pageSize       = $this->request->post("pageSize", 50);
        $orderChannel   = $this->request->get("orderChannel", "");
        $orderStatus    = $this->request->get("orderStatus", []);
        $deliveryStatus = $this->request->get("deliveryStatus", []);
        $refundStatus   = $this->request->get("refundStatus", []);
        $timeCls        = $this->request->get("timeCls", "");
        $startTime      = $this->request->get("startTime", "");
        $endTime        = $this->request->get("endTime", "");
        $search_cls     = $this->request->get("search_cls", "order_id");
        $keyword        = $this->request->get("keyword", "");
        $sort           = $this->request->get("sort", "created_at|desc");

        $pageSize = $pageSize > 500 ? 500 : $pageSize;
        $offset   = ($page - 1) * $pageSize;

        $params = [
            "page"           => $page,
            "pageSize"       => $pageSize,
            "orderChannel"   => $orderChannel,
            "orderStatus"    => $orderStatus,
            "deliveryStatus" => $deliveryStatus,
            "refundStatus"   => $refundStatus,
            "timeCls"        => $timeCls,
            "startTime"      => $startTime,
            "endTime"        => $endTime,
            "search_cls"     => $search_cls,
            "keyword"        => $keyword,
            "sort"           => $sort,
        ];
        $result = $this->orderService->orderList($params);
        // dd($result["data"]->items()->toArray());
        $viewParams = [
            "paginator"      => $result["data"],
            "offset"         => (int) $offset,
            "pageSize"       => (int) $pageSize,
            "orderChannel"   => $orderChannel,
            "orderStatus"    => implode(",", $orderStatus),
            "deliveryStatus" => implode(",", $deliveryStatus),
            "refundStatus"   => implode(",", $refundStatus),
            "timeCls"        => $timeCls,
            "startTime"      => $startTime,
            "endTime"        => $endTime,
            "search_cls"     => $search_cls,
            "keyword"        => $keyword,
            "sort"           => $sort,
        ];
        return view("order.list")->with($viewParams);
    }

    public function orderWList(): View
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
        $result = $this->orderService->orderWList($params);
        // dd($result["data"]->items());
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
        return view("order.wList")->with($viewParams);
    }
}
