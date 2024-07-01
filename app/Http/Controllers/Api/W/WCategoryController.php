<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\CategoryErrorMessageConstant;
use App\Constants\Constant1688;
use App\Constants\HttpConstant;
use App\Constants\ProductConstant;
use App\Constants\ProductErrorMessageConstant;
use App\Http\Controllers\Controller;
use App\Services\Service1688Category;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WCategoryController extends Controller
{
    private Request $request;
    private Service1688Category $service1688Category;

    function __construct(Request $request, Service1688Category $service1688Category)
    {
        $this->request             = $request;
        $this->service1688Category = $service1688Category;
    }

    public function getAllCategory(): JsonResponse
    {
        $parent_cate_id = $this->request->get("parent_cate_id", null);
        $result = $this->service1688Category->getAllCategory($parent_cate_id);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getTreeCategory(int $categoryId = 1038378): JsonResponse
    {
        $result = $this->service1688Category->getTreeCategory($categoryId);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getMallCategory(int $categoryId = 1038378): JsonResponse
    {
        $result = $this->service1688Category->getMallCategory($categoryId);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getMappingCategory(string $channel = ProductConstant::MAPPING_OC_CHANNEL): JsonResponse
    {
        $result = $this->service1688Category->getMappingCategory($channel);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getW(): JsonResponse
    {
        $cate_first  = $this->request->post("cate_first", "");
        $cate_second = $this->request->post("cate_second", "");
        $cate_third  = $this->request->post("cate_third", "");
        $cate_fourth = $this->request->post("cate_fourth", "");
        $keyword     = trim($this->request->post("w_cate_keyword", ""));

        if( !$cate_first && !$keyword ){
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], "1차 분류 또는 검색어를 입력하세요.");
        }

        $params      = [
            "cate_first"  => $cate_first,
            "cate_second" => $cate_second,
            "cate_third"  => $cate_third,
            "cate_fourth" => $cate_fourth,
            "keyword"     => $keyword,
        ];
        $result = $this->service1688Category->getW($params);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function wMapping(): JsonResponse
    {
        try {
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
            $result = $this->service1688Category->wMapping($params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function getDepth(int $categoryId): JsonResponse
    {
        $result = $this->service1688Category->getDepth($categoryId);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function getWDepth(): JsonResponse
    {
        try {
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
            $result = $this->service1688Category->getWDepth($params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function getInfos(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'categoryIds' => 'required|array',
            ], [
                'categoryIds.required' => CategoryErrorMessageConstant::getNotHaveErrorMessage("CATEGORYID"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $result = $this->service1688Category->getInfos($this->request->post("categoryIds"));
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function weightSave(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'category_ids' => 'required|string',
                'weight'       => 'required|int',
            ], [
                'category_ids.required' => CategoryErrorMessageConstant::getNotHaveErrorMessage("CATEGORYID"),
                'weight.required'       => CategoryErrorMessageConstant::getNotHaveErrorMessage("WEIGHT"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $categoryIds = explode(",", $this->request->post("category_ids"));
            $weight      = $this->request->post("weight");
            $result      = $this->service1688Category->weightSave($categoryIds, $weight);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function weightRemove(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'category_ids' => 'required|array',
            ], [
                'category_ids.required' => CategoryErrorMessageConstant::getNotHaveErrorMessage("CATEGORYID"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $categoryIds = $this->request->post("category_ids");
            $result      = $this->service1688Category->weightRemove($categoryIds);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function topList(int $categoryId): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'country'   => 'required|string',
                'page_size' => 'required|int',
            ], [
                "country"   => ProductErrorMessageConstant::getNotHaveErrorMessage("COUNTRY"),
                "page_size" => ProductErrorMessageConstant::getNotHaveErrorMessage("PAGESIZE"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $country  = $this->request->get("country", Constant1688::LANGUAGE_KO);
            $pageSize = $this->request->get("page_size") > 20 ? 20 : $this->request->get("page_size");
            $result   = $this->service1688Category->topList($categoryId, $country, $pageSize);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function topKeyword(int $categoryId): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'country' => 'required|string',
            ], [
                "country" => ProductErrorMessageConstant::getNotHaveErrorMessage("COUNTRY"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $country = $this->request->get("country", Constant1688::LANGUAGE_KO);
            $result = $this->service1688Category->topKeyword($categoryId, $country);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function sendMallUpdate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'cateParams'              => 'required|array',
                'cateParams.*.categoryId' => 'required|int',
                'cateParams.*.ocPublic'   => 'required|string',
                'cateParams.*.ocPrivate'  => 'required|string',
                'cateParams.*.esW'        => 'required|string',
                'cateParams.*.esDropHub'  => 'required|string',
            ], [
                'cateParams.required'              => CategoryErrorMessageConstant::getNotHaveErrorMessage("CATEPARAMS"),
                'cateParams.*.categoryId.required' => CategoryErrorMessageConstant::getNotHaveErrorMessage("CATEGORYID"),
                'cateParams.*.ocPublic.required'   => CategoryErrorMessageConstant::getNotHaveErrorMessage("OCPUBLIC"),
                'cateParams.*.ocPrivate.required'  => CategoryErrorMessageConstant::getNotHaveErrorMessage("OCPRIVATE"),
                'cateParams.*.esW.required'        => CategoryErrorMessageConstant::getNotHaveErrorMessage("ESW"),
                'cateParams.*.esDropHub.required'  => CategoryErrorMessageConstant::getNotHaveErrorMessage("ESDROPHUB"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $cateParams = $this->request->post("cateParams");
            $result      = $this->service1688Category->sendMallUpdate($cateParams);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }
}
