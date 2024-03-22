<?php

namespace App\Http\Controllers;

use App\Constants\HttpConstant;
use App\Constants\ProductConstant;
use App\Services\Service1688;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    private Request $request;
    private Service1688 $service1688;

    function __construct(Request $request, Service1688 $service1688)
    {
        $this->request           = $request;
        $this->service1688 = $service1688;
    }

    public function getAllCategory(): JsonResponse
    {
        $result = $this->service1688->getAllCategory();
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getTreeCategory(int $categoryId = 1038378): JsonResponse
    {
        $result = $this->service1688->getTreeCategory($categoryId);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getMallCategory(int $categoryId = 1038378): JsonResponse
    {
        $result = $this->service1688->getMallCategory($categoryId);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getMappingCategory(string $channel = ProductConstant::MAPPING_OC_CHANNEL): JsonResponse
    {
        $result = $this->service1688->getMappingCategory($channel);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getProductData(int $offerId): JsonResponse
    {
        $result = $this->service1688->getProductData($offerId);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }
}
