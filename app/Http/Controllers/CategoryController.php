<?php

namespace App\Http\Controllers;

use App\Constants\CategoryConstant;
use App\Constants\ProductConstant;
use App\Http\Controllers\Controller;
use App\Services\Service1688Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    private Request $request;
    private Service1688Category $service1688Category;

    function __construct(
        Request $request,
        Service1688Category $service1688Category
    )
    {
        $this->request             = $request;
        $this->service1688Category = $service1688Category;
    }

    public function manage(): View
    {
        $page           = $this->request->post("page", 1);
        $pageSize       = $this->request->post("pageSize", 50);
        $keyword        = $this->request->get("keyword", "");
        $mapping_status = $this->request->get("mapping_status", ProductConstant::TRANS_STATUS_Y);
        $cate_first     = $this->request->get("cate_first", "");
        $cate_second    = $this->request->get("cate_second", "");
        $cate_third     = $this->request->get("cate_third", "");
        $offset         = ($page - 1) * $pageSize;

        $params = [
            "page"           => $page,
            "pageSize"       => $pageSize,
            "keyword"        => $keyword,
            "mapping_status" => $mapping_status,
            "cate_first"     => $cate_first,
            "cate_second"    => $cate_second,
            "cate_third"     => $cate_third
        ];
        $result = $this->service1688Category->cateList($params);
        $viewParams = [
            "mapping_status" => $mapping_status,
            "keyword"        => $keyword,
            "offset"         => $offset,
            "datas"          => $result["data"]["paginator"],
            "firstCateObjs"  => $result["data"]["firstCateObjs"],
            "secondCateObjs" => $result["data"]["secondCateObjs"],
            "thirdCateObjs"  => $result["data"]["thirdCateObjs"],
            "cate_first"     => $cate_first,
            "cate_second"    => $cate_second,
            "cate_third"     => $cate_third
        ];
        return view("category.manage")->with($viewParams);
    }

    public function weightList(): View
    {
        $page          = $this->request->post("page", 1);
        $pageSize      = $this->request->post("pageSize", 50);
        $keyword       = $this->request->get("keyword", "");
        $weight_status = $this->request->get("weight_status", "");
        $cate_first    = $this->request->get("cate_first", "");
        $cate_second   = $this->request->get("cate_second", "");
        $cate_third    = $this->request->get("cate_third", "");
        $offset        = ($page - 1) * $pageSize;

        $params = [
            "page"          => $page,
            "pageSize"      => $pageSize,
            "keyword"       => $keyword,
            "weight_status" => $weight_status,
            "cate_first"    => $cate_first,
            "cate_second"   => $cate_second,
            "cate_third"    => $cate_third
        ];
        $result = $this->service1688Category->weightList($params);
        $viewParams = [
            "weight_status"  => $weight_status,
            "keyword"        => $keyword,
            "offset"         => $offset,
            "datas"          => $result["data"]["paginator"],
            "firstCateObjs"  => $result["data"]["firstCateObjs"],
            "secondCateObjs" => $result["data"]["secondCateObjs"],
            "thirdCateObjs"  => $result["data"]["thirdCateObjs"],
            "cate_first"     => $cate_first,
            "cate_second"    => $cate_second,
            "cate_third"     => $cate_third
        ];
        return view("category.weightList")->with($viewParams);
    }
}
