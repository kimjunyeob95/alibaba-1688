<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\HttpConstant;
use App\Constants\OrderErrorMessageConstant;
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

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "주문 업데이트 요청 완료\r\n주문 건이 많을 경우 업데이트에 시간이 소요될 수 있습니다."));
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

    public function orderInfo(string $orderId): JsonResponse
    {
        try {
            $result = $this->orderService->orderInfo($orderId);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function orderInfoUpdate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'orderId'              => 'required|string',
                'channelPrices'        => 'required|array',
                'orderChannel'         => 'required|string',
                'channelOrderId'       => 'required|string',
                'buyerName'            => 'required|string',
                'deliveryPrice'        => 'required|numeric',
                'buyerClearanceNumber' => 'required|string',
                'buyerNumber'          => 'required|string',
                'buyerPhone'           => 'required|string',
                'buyerAddress'         => 'required|string',
                'buyerZipcode'         => 'required|string',
                'buyerMemo'            => 'required|string',
            ], [
                'orderId.required'              => OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_ID"),
                'channelPrices.required'        => OrderErrorMessageConstant::getNotHaveErrorMessage("CHANNEL_PRICES"),
                'orderChannel.required'         => OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_CHANNEL"),
                'channelOrderId.required'       => OrderErrorMessageConstant::getNotHaveErrorMessage("CHANNEL_ORDER_ID"),
                'deliveryPrice.required'        => OrderErrorMessageConstant::getNotHaveErrorMessage("DELIVERY_PRICE"),
                'buyerName.required'            => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_NAME"),
                'buyerClearanceNumber.required' => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_CLEARANCE_NUMBER"),
                'buyerNumber.required'          => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_NUMBER"),
                'buyerPhone.required'           => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_PHONE"),
                'buyerAddress.required'         => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_ADDRESS"),
                'buyerZipcode.required'         => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_ZIPCODE"),
                'buyerMemo.required'            => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_MEMO"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $result = $this->orderService->orderInfoUpdate($this->request->all());
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
          

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "주문 업데이트 요청 완료"));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function orderWappInfo(string $orderId): JsonResponse
    {
        try {
            $result = $this->orderService->orderWappInfo($orderId);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function orderLogisticsInfo(string $orderId): JsonResponse
    {
        try {
            $result = $this->orderService->orderLogisticsInfo($orderId);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function orderCancel(string $orderId): JsonResponse
    {
        try {
            $result = $this->orderService->orderCancel($orderId);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function orderInfoChannelUpdate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'order_id'                                 => 'required|string',
                'change_channels'                          => 'required|array',
                'change_channels.*.buyer_address'          => 'required|string',
                'change_channels.*.buyer_clearance_number' => 'required|string',
                'change_channels.*.buyer_memo'             => 'required|string',
                'change_channels.*.buyer_name'             => 'required|string',
                'change_channels.*.buyer_number'           => 'required|string',
                'change_channels.*.buyer_phone'            => 'required|string',
                'change_channels.*.buyer_zipcode'          => 'required|string',
                'change_channels.*.channel_order_id'       => 'required|string',
                'change_channels.*.delivery_price'         => 'required|string',
            ], [
                'order_id.required'                                 => OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_ID"),
                'change_channels.required'                          => OrderErrorMessageConstant::getNotHaveErrorMessage("CHANGE_CHANNELS"),
                'change_channels.*.buyer_address.required'          => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_CLEARANCE_NUMBER"),
                'change_channels.*.buyer_clearance_number.required' => OrderErrorMessageConstant::getNotHaveErrorMessage("DELIVERY_PRICE"),
                'change_channels.*.buyer_memo.required'             => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_MEMO"),
                'change_channels.*.buyer_name.required'             => OrderErrorMessageConstant::getNotHaveErrorMessage("DELIVERY_PRICE"),
                'change_channels.*.buyer_number.required'           => OrderErrorMessageConstant::getNotHaveErrorMessage("DELIVERY_PRICE"),
                'change_channels.*.buyer_phone.required'            => OrderErrorMessageConstant::getNotHaveErrorMessage("DELIVERY_PRICE"),
                'change_channels.*.buyer_zipcode.required'          => OrderErrorMessageConstant::getNotHaveErrorMessage("DELIVERY_PRICE"),
                'change_channels.*.channel_order_id.required'       => OrderErrorMessageConstant::getNotHaveErrorMessage("DELIVERY_PRICE"),
                'change_channels.*.delivery_price.required'         => OrderErrorMessageConstant::getNotHaveErrorMessage("DELIVERY_PRICE"),

                'orderId.required'              => OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_ID"),
                'channelPrices.required'        => OrderErrorMessageConstant::getNotHaveErrorMessage("CHANNEL_PRICES"),
                'orderChannel.required'         => OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_CHANNEL"),
                'channelOrderId.required'       => OrderErrorMessageConstant::getNotHaveErrorMessage("CHANNEL_ORDER_ID"),
                'deliveryPrice.required'        => OrderErrorMessageConstant::getNotHaveErrorMessage("DELIVERY_PRICE"),
                'buyerName.required'            => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_NAME"),
                'buyerClearanceNumber.required' => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_CLEARANCE_NUMBER"),
                'buyerNumber.required'          => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_NUMBER"),
                'buyerPhone.required'           => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_PHONE"),
                'buyerAddress.required'         => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_ADDRESS"),
                'buyerZipcode.required'         => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_ZIPCODE"),
                'buyerMemo.required'            => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_MEMO"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $result = $this->orderService->orderInfoUpdate($this->request->all());
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
          

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "주문 업데이트 요청 완료"));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }
}
