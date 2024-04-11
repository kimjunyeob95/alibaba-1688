<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\HttpConstant;
use App\Constants\ProductConstant;
use App\Http\Controllers\Controller;
use App\Services\Service1688Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WCategoryController extends Controller
{
    private Request $request;
    private Service1688Product $service1688Product;

    function __construct(Request $request, Service1688Product $service1688Product)
    {
        $this->request            = $request;
        $this->service1688Product = $service1688Product;
    }

    public function getAllCategory(): JsonResponse
    {
        $result = $this->service1688Product->getAllCategory();
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getTreeCategory(int $categoryId = 1038378): JsonResponse
    {
        $result = $this->service1688Product->getTreeCategory($categoryId);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getMallCategory(int $categoryId = 1038378): JsonResponse
    {
        $result = $this->service1688Product->getMallCategory($categoryId);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getMappingCategory(string $channel = ProductConstant::MAPPING_OC_CHANNEL): JsonResponse
    {
        $result = $this->service1688Product->getMappingCategory($channel);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }
}
