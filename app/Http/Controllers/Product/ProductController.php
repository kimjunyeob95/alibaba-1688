<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Services\Product\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    private Request $request;
    private ProductService $productService;

    function __construct(Request $request, ProductService $productService)
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
}
