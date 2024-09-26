<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\HttpConstant;
use App\Http\Controllers\Controller;
use App\Services\Wms\WmsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function getTarffi(string $hsCode): JsonResponse
    {
        try {
            $result  = $this->wmsService->getTariff($hsCode);

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
