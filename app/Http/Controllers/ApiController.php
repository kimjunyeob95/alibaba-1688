<?php

namespace App\Http\Controllers;

use App\Abstracts\ApiModuleAbstract;
use App\Constants\HttpConstant;
use App\Constants\ProductConstant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    private Request $request;
    private ApiModuleAbstract $apiModuleAbstract;

    function __construct(Request $request, ApiModuleAbstract $apiModuleAbstract)
    {
        $this->request           = $request;
        $this->apiModuleAbstract = $apiModuleAbstract;
    }

    public function getAllCategory(): JsonResponse
    {
        $result = $this->apiModuleAbstract->getAllCategory();
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getTreeCategory(int $categoryId = 1038378): JsonResponse
    {
        $result = $this->apiModuleAbstract->getTreeCategory($categoryId);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getMallCategory(int $categoryId = 1038378): JsonResponse
    {
        $result = $this->apiModuleAbstract->getMallCategory($categoryId);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getMappingCategory(string $channel = ProductConstant::MAPPING_OC_CHANNEL): JsonResponse
    {
        $result = $this->apiModuleAbstract->getMappingCategory($channel);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getProductData(int $offerId): JsonResponse
    {
        $result = $this->apiModuleAbstract->getProductData($offerId);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }
}
