<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Services\Product\ProductV1;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    private Request $request;
    private ProductV1 $productService;

    function __construct(Request $request, ProductV1 $productService)
    {
        $this->request        = $request;
        $this->productService = $productService;
    }

    public function getPrdList(): View
    {
        $page     = $this->request->post("page", 1);
        $pageSize = $this->request->post("pageSize", 30);
        $offset   = ($page - 1) * $pageSize;

        $params = [
            "page"     => $page,
            "pageSize" => $pageSize,
        ];
        $result = $this->productService->getPrdList($params);
        $viewParams = [
            "datas"         => $result,
            "offset"        => (int) $offset,
            "totalCnt"      => (int) $result->total(),
        ];
        return view("product.prdList")->with($viewParams);
    }

    public function getPrdDetail(int $offerId): View
    {
        $result = $this->productService->getPrdDetail($offerId);
        if( $result["isSuccess"] == false ){
            abort(404);
        } else {
            $viewParams = [
                "prdObj" => $result["data"]
            ];
        }
        return view("product.prdDetail")->with($viewParams);
    }

    public function keywordQuery(): View
    {
        $search_cls = $this->request->get("search_cls", "");
        $keyword    = $this->request->get("keyword", "");
        $sort       = $this->request->get("sort", "monthSold|desc");
        $page       = $this->request->get("page", 1);
        $pageSize   = $this->request->get("pageSize", 50);
        
        $params = [
            "search_cls" => $search_cls,
            "keyword"    => $keyword,
            "sort"       => $sort,
            "page"       => $page,
            "pageSize"   => $pageSize,
        ];
        $result = $this->productService->getKeywordQuery($params);
        $viewParams = [
            "datas"        => $result["data"]["result"]["result"]["data"],
            "totalRecords" => $result["data"]["result"]["result"]["totalRecords"],
            "totalPage"    => $result["data"]["result"]["result"]["totalPage"],
        ];
        dd($viewParams);
        return view("product.prdKeywordQuery")->with($viewParams);
    }
}
