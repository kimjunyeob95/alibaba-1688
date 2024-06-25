<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\HttpConstant;
use App\Constants\ProductErrorMessageConstant;
use App\Http\Controllers\Controller;
use App\Services\CollectService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WCollectController extends Controller
{
    private Request $request;
    private CollectService $collectService;

    function __construct(Request $request, CollectService $collectService)
    {
        $this->request        = $request;
        $this->collectService = $collectService;
    }

    public function palletPrdList(int $palletId): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'begin_page' => 'required|int',
                'page_size'  => 'required|int',
            ], [
                "begin_page" => ProductErrorMessageConstant::getNotHaveErrorMessage("BEGINPAGE"),
                "page_size"  => ProductErrorMessageConstant::getNotHaveErrorMessage("PAGESIZE"),
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $beginPage = $this->request->get("begin_page", 1);
            $pageSize  = $this->request->get("page_size", 50);

            $params = [
                "pallet_id"  => $palletId,
                "begin_page" => $beginPage,
                "page_size"  => $pageSize > 50 ? 50 : $pageSize,
            ];

            $result = $this->collectService->palletPrdList($params);

            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function palletValidation(int $palletId): JsonResponse
    {
        try {
            $result = $this->collectService->palletValidation($palletId);

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
