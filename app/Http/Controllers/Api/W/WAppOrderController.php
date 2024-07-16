<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\HttpConstant;
use App\Http\Controllers\Controller;
use App\Services\Order\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Process;

class WAppOrderController extends Controller
{
    private Request $request;
    private OrderService $orderService;
    private string $phpAlias;

    function __construct(Request $request, OrderService $orderService)
    {
        $this->request      = $request;
        $this->orderService = $orderService;
        $this->phpAlias     = env("PHP_ALIAS", "php80");
    }

    public function orderUpdate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'orderIds' => 'required|array',
            ], [
                'orderIds.required' => 'orderIds를 입력하세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $orderIds = $this->request->post("orderIds");
            $options  = "--func=orderUpdate --orderids=" . helperEscape(implode(",", $orderIds));
            $command  = "nohup " . $this->phpAlias . " artisan order_command " . $options . " > /dev/null 2>&1 &";
            $process  = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "주문 업데이트 요청 완료"));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function orderPayLinkCreate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'orderIds' => 'required|array',
                'payWay'   => 'required|string',
            ], [
                'orderIds.required' => 'orderIds를 입력하세요.',
                'payWay.required'   => 'payWay를 입력하세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $orderIds = $this->request->post("orderIds");
            $payWay   = $this->request->post("payWay");
            $param    = [
                "orderIds" => $orderIds,
                "payWay"   => $payWay,
            ];

            $result  = $this->orderService->orderPayLinkCreate($param);

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
