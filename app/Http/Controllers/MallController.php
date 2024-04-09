<?php

namespace App\Http\Controllers;

use App\Constants\HttpConstant;
use App\Constants\OrderErrorMessageConstant;
use App\Services\MallApiService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MallController extends Controller
{
    private Request $request;
    private MallApiService $mallApiService;

    function __construct(Request $request, MallApiService $mallApiService)
    {
        $this->request        = $request;
        $this->mallApiService = $mallApiService;
    }

    public function tokenCreate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'user_id' => 'required',
            ], [
                'user_id.required' => '아이디를 입력하세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $credentials = $this->request->only(["user_id"]);
            $params = [
                "user_id" => $credentials["user_id"]
            ];
            $result = $this->mallApiService->tokenCreate($params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function productRegist()
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'offer_ids' => 'required|array',
            ], [
                'offer_ids.required' => 'offer_ids를 전달해주세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $result   = $this->mallApiService->productRegist($this->request->post("offer_ids"));
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function orderInfo(int $orderId): JsonResponse
    {
        try {
            $result = $this->mallApiService->orderInfo($orderId);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function orderCreate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'cargoParamList'             => 'required|array',
                'cargoParamList.*.offer_id'  => 'required|int',
                'cargoParamList.*.option_id' => 'required|int',
                'cargoParamList.*.quantity'  => 'required|int',
                'receive_name'               => 'required|string',
                'receive_tell'               => 'required|string',
                'receive_phone'              => 'required|string',
            ], [
                'cargoParamList.required'     => OrderErrorMessageConstant::getNotHaveErrorMessage("CARGOPARAMLIST"),
                'images.*.offer_id.required'  => OrderErrorMessageConstant::getNotHaveErrorMessage("OFFER_ID"),
                'images.*.option_id.required' => OrderErrorMessageConstant::getNotHaveErrorMessage("OPTION_ID"),
                'images.*.quantity.required'  => OrderErrorMessageConstant::getNotHaveErrorMessage("QUANTITY"),
                'receive_name.required'       => OrderErrorMessageConstant::getNotHaveErrorMessage("RECEIVE_NAME"),
                'receive_tell.required'       => OrderErrorMessageConstant::getNotHaveErrorMessage("RECEIVE_TELL"),
                'receive_phone.required'      => OrderErrorMessageConstant::getNotHaveErrorMessage("RECEIVE_PHONE"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $result = $this->mallApiService->orderCreate($this->request->all());
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
