<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\HttpConstant;
use App\Constants\WmsConstant;
use App\Constants\WmsErrorMessageConstant;
use App\Http\Controllers\Controller;
use App\Services\Wms\WmsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WAppWmsController extends Controller
{
    private Request $request;
    private WmsService $wmsService;
    private string $phpAlias;

    function __construct(Request $request, WmsService $wmsService)
    {
        $this->request      = $request;
        $this->wmsService = $wmsService;
        $this->phpAlias     = env("PHP_ALIAS", "php80");
    }

    public function apiHsCodeList(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'begin_page' => 'required|int',
                'page_size'  => 'required|int',
            ], [
                'begin_page.required' => WmsErrorMessageConstant::getNotHaveErrorMessage("BEGIN_PAGE"),
                'page_size.required'  => WmsErrorMessageConstant::getNotHaveErrorMessage("PAGE_SIZE"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $page       = $this->request->get("begin_page", 1);
            $pageSize   = $this->request->get("page_size", 50);
            $pageSize   = $pageSize > 500 ? 500 : $pageSize;
            $search_cls = $this->request->get("search_cls", WmsConstant::HSCODE_SEARCH_TYPE_KO);
            $keyword    = $this->request->get("keyword", "");
            $sort       = $this->request->get("sort", "property_code_name|desc");

            $params = [
                "page"       => $page,
                "pageSize"   => $pageSize,
                "search_cls" => $search_cls,
                "keyword"    => $keyword,
                "sort"       => $sort,
            ];
            $result  = $this->wmsService->apiHsCodeList($params);

            return helpers_json_response(HttpConstant::OK, $result);
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function getTariff(string $hsCode): JsonResponse
    {
        try {
            $result = $this->wmsService->getTariff($hsCode);

            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function apiTarffi(string $channel, string $hsCode): JsonResponse
    {
        try {
            $result = $this->wmsService->getTariff($hsCode);

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
