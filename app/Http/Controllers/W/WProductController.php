<?php

namespace App\Http\Controllers\W;

use App\Constants\HttpConstant;
use App\Constants\ProductConstant;
use App\Http\Controllers\Controller;
use App\Services\Service1688Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WProductController extends Controller
{
    private Request $request;
    private Service1688Product $service1688Product;

    function __construct(Request $request, Service1688Product $service1688Product)
    {
        $this->request            = $request;
        $this->service1688Product = $service1688Product;
    }

    public function apiPrdList(): JsonResponse
    {
        try {
            $page           = $this->request->get("page", 1);
            $pageSize       = $this->request->get("pageSize", 50);
            if( $pageSize > 50 ) $pageSize = 50;
            $search_cls     = $this->request->get("search_cls", "prd_name_trans");
            $keyword        = $this->request->get("keyword", "");
            $trans_status   = $this->request->get("trans_status", ProductConstant::TRANS_STATUE_Y);
            
            $params = [
                "page"         => $page,
                "pageSize"     => $pageSize,
                "search_cls"   => $search_cls,
                "keyword"      => $keyword,
                "trans_status" => $trans_status,
            ];
            $result = $this->service1688Product->apiPrdList($params);

            return helpers_json_response(HttpConstant::OK, helpers_success_message($result));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }
}
