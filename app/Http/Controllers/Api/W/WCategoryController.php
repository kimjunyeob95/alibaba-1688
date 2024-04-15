<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\CategoryErrorMessageConstant;
use App\Constants\HttpConstant;
use App\Constants\ImageErrorMessageConstant;
use App\Constants\ProductConstant;
use App\Http\Controllers\Controller;
use App\Services\Service1688Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $parent_cate_id = $this->request->get("parent_cate_id", null);
        $result = $this->service1688Product->getAllCategory($parent_cate_id);
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

    public function getW(): JsonResponse
    {
        $validator = Validator::make($this->request->all(), [
            'cate_first'  => 'required|string',
        ], [
            'cate_first.required'  => CategoryErrorMessageConstant::getNotHaveErrorMessage("CATE_FIRST"),
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        $params = [
            "cate_first"  => $this->request->post("cate_first"),
            "cate_second" => $this->request->post("cate_second", ""),
            "cate_third"  => $this->request->post("cate_third", ""),
            "cate_fourth" => $this->request->post("cate_fourth", ""),
            "keyword"     => $this->request->post("w_cate_keyword", ""),
        ];
        $result = $this->service1688Product->getW($params);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function wMapping(): JsonResponse
    {
        $validator = Validator::make($this->request->all(), [
            'category_ids' => 'required|string',
            'w_cate_id'    => 'required|int',
        ], [
            'category_ids.required' => CategoryErrorMessageConstant::getNotHaveErrorMessage("CATEGORYID"),
            'w_cate_id.required'    => CategoryErrorMessageConstant::getNotHaveErrorMessage("W_CATEGORYID"),
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        $params = [
            "category_ids" => explode(",", $this->request->post("category_ids")),
            "w_cate_id"    => $this->request->post("w_cate_id"),
        ];
        $result = $this->service1688Product->wMapping($params);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getDepth(int $categoryId): JsonResponse
    {
        $result = $this->service1688Product->getDepth($categoryId);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getWDepth(): JsonResponse
    {
        $validator = Validator::make($this->request->all(), [
            'level'     => 'required|int',
            'cate_name' => 'required|string',
        ], [
            'level.required'     => CategoryErrorMessageConstant::getNotHaveErrorMessage("LEVEL"),
            'cate_name.required' => CategoryErrorMessageConstant::getNotHaveErrorMessage("CATEGORY_NAME"),
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        $params = [
            "level"     => $this->request->post("level"),
            "cate_name" => $this->request->post("cate_name"),
        ];
        $result = $this->service1688Product->getWDepth($params);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getInfos(): JsonResponse
    {
        $validator = Validator::make($this->request->all(), [
            'categoryIds' => 'required|array',
        ], [
            'categoryIds.required' => CategoryErrorMessageConstant::getNotHaveErrorMessage("CATEGORYID"),
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        $result = $this->service1688Product->getInfos($this->request->post("categoryIds"));
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }
}
