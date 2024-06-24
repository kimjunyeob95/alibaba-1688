<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\HttpConstant;
use App\Http\Controllers\Controller;
use App\Services\CollectService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WCollectController extends Controller
{
    private Request $request;
    private CollectService $collectService;

    function __construct(Request $request, CollectService $collectService)
    {
        $this->request        = $request;
        $this->collectService = $collectService;
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
