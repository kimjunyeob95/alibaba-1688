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
        $page                = $this->request->post("page", 1);
        $pageSize            = $this->request->post("pageSize", 50);
        $search_cls          = $this->request->get("search_cls", "offer_id");
        $w_type              = $this->request->get("w_type", "");
        $keyword             = $this->request->get("keyword", "");
        $collect_status      = $this->request->get("collect_status", "");
        $trans_status        = $this->request->get("trans_status", "");
        $mapping_status      = $this->request->get("mapping_status", "");
        $prd_status          = $this->request->get("prd_status", "");
        $mdPrice_status      = $this->request->get("mdPrice_status", "");
        $weight_status       = $this->request->get("weight_status", "");
        $sort                = $this->request->get("sort", "updated_at|desc");
        $inspect_status      = $this->request->get("inspect_status", "");
        $inspect_img_status  = $this->request->get("inspect_img_status", "");
        $inspect_prd_status  = $this->request->get("inspect_prd_status", "");
        $inspect_gosi_status = $this->request->get("inspect_gosi_status", "");
        $cate_first          = $this->request->get("cate_first", "");
        $cate_second         = $this->request->get("cate_second", "");
        $cate_third          = $this->request->get("cate_third", "");
        $no_send_channel     = $this->request->get("no_send_channel", "");
        $quantity_count      = $this->request->get("quantity_count", "");
        $offset              = ($page - 1) * $pageSize;

        $params = [
            "page"                => $page,
            "pageSize"            => $pageSize,
            "search_cls"          => $search_cls,
            "inspect_status"      => $inspect_status,
            "w_type"              => $w_type,
            "keyword"             => $keyword,
            "collect_status"      => $collect_status,
            "trans_status"        => $trans_status,
            "mapping_status"      => $mapping_status,
            "prd_status"          => $prd_status,
            "mdPrice_status"      => $mdPrice_status,
            "weight_status"       => $weight_status,
            "sort"                => $sort,
            "inspect_img_status"  => $inspect_img_status,
            "inspect_prd_status"  => $inspect_prd_status,
            "inspect_gosi_status" => $inspect_gosi_status,
            "cate_first"          => $cate_first,
            "cate_second"         => $cate_second,
            "cate_third"          => $cate_third,
            "no_send_channel"     => $no_send_channel,
            "quantity_count"      => $quantity_count,
        ];
        $result = $this->wMessageService->list($params);

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
            "search_cls"          => $search_cls,
            "w_type"              => $w_type,
            "keyword"             => $keyword,
            "collect_status"      => $collect_status,
            "trans_status"        => $trans_status,
            "mapping_status"      => $mapping_status,
            "weight_status"       => $weight_status,
            "prd_status"          => $prd_status,
            "mdPrice_status"      => $mdPrice_status,
            "inspect_status"      => $inspect_status,
            "inspect_img_status"  => $inspect_img_status,
            "inspect_prd_status"  => $inspect_prd_status,
            "inspect_gosi_status" => $inspect_gosi_status,
            "sort"                => $sort,
            "firstCateObjs"       => $result["firstCateObjs"],
            "secondCateObjs"      => $result["secondCateObjs"],
            "thirdCateObjs"       => $result["thirdCateObjs"],
            "cate_first"          => $cate_first,
            "cate_second"         => $cate_second,
            "cate_third"          => $cate_third,
            "no_send_channel"     => $no_send_channel,
            "quantity_count"      => $quantity_count,
        ];

        return view("product.prdList")->with($viewParams);
    }
}
