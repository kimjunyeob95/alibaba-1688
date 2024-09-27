<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\HttpConstant;
use App\Constants\QueueConstant;
use App\Http\Controllers\Controller;
use App\Jobs\WmessageJob;
use App\Services\Message\WMessageService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;

class WMessageController extends Controller
{
    private Request $request;
    private WMessageService $wMessageService;

    function __construct(Request $request, WMessageService $wMessageService)
    {
        $this->request         = $request;
        $this->wMessageService = $wMessageService;
    }

    public function message(): JsonResponse
    {
        try {
            Queue::push(new WmessageJob($this->request->all()));

            return helpers_json_response(HttpConstant::OK);
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function detail(int $id): JsonResponse
    {
        try {
            $result = $this->wMessageService->detail($id);

            return helpers_json_response(HttpConstant::OK, $result);
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function taobaoCallback(): JsonResponse
    {
        try {
            $this->wMessageService->taobaoCallback($this->request->all());

            return helpers_json_response(HttpConstant::OK);
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }
}
