<?php

namespace App\Http\Controllers\Api;

use App\Constants\CategoryErrorMessageConstant;
use App\Constants\HttpConstant;
use App\Http\Controllers\Controller;
use App\Services\Mall\MallCategoryApiService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MallCategoryController extends Controller
{
    private Request $request;
    private MallCategoryApiService $mallCategoryApiService;

    function __construct(Request $request, MallCategoryApiService $mallCategoryApiService)
    {
        $this->request                = $request;
        $this->mallCategoryApiService = $mallCategoryApiService;
    }

    public function list(): JsonResponse
    {
        try {   
            $keyword     = $this->request->post("keyword", "");
            $cate_first  = $this->request->post("cate_first", "");
            $cate_second = $this->request->post("cate_second", "");
            $cate_third  = $this->request->post("cate_third", "");
            $cate_fourth = $this->request->post("cate_fourth", "");

            if( $keyword == "" && $cate_first == "" ){
                throw new Exception("검색어 또는 카테고리를 하나 선택하세요.");
            }

            $params = [
                "keyword"     => $keyword,
                "cate_first"  => $cate_first,
                "cate_second" => $cate_second,
                "cate_third"  => $cate_third,
                "cate_fourth" => $cate_fourth,
            ];
            $result = $this->mallCategoryApiService->channelCateList($params);

            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function depth(): JsonResponse
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

            $result = $this->mallCategoryApiService->channelCateDepth($params);

            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function mapping(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'wAppCateCode'    => 'required|string',
                'channelCateCode' => 'required|string',
            ], [
                'wAppCateCode.required'    => CategoryErrorMessageConstant::getNotHaveErrorMessage("W_CATEGORYID"),
                'channelCateCode.required' => CategoryErrorMessageConstant::getNotHaveErrorMessage("CHANNELCATECODE"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
    
            $params = [
                "wAppCateCode"    => $this->request->post("wAppCateCode"),
                "channelCateCode" => $this->request->post("channelCateCode"),
            ];

            $result = $this->mallCategoryApiService->channelMapping($params);

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
