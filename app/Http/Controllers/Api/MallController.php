<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpConstant;
use App\Constants\OrderErrorMessageConstant;
use App\Http\Controllers\Controller;
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
                'type'      => 'required|string',
            ], [
                'offer_ids.required' => 'offer_ids를 전달해주세요.',
                'type.required'      => 'type을 전달해주세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $result   = $this->mallApiService->productRegist($this->request->post("offer_ids"), $this->request->post("type"));
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
                'offer_id'                    => 'required|int',
                'optionParamList'             => 'required|array',
                'optionParamList.*.option_id' => 'required|int',
                'optionParamList.*.quantity'  => 'required|int',
                'option_price'                => 'required|int',
                'buyer_name'                  => 'required|string',
                'buyer_clearance_number'      => 'required|string',
                'buyer_number'                => 'required|string',
                'buyer_phone'                 => 'required|string',
                'buyer_zipcode'               => 'required|string',
                'buyer_address'               => 'required|string',
                'buyer_memo'                  => 'required|string',
            ], [
                'offer_id.required'                    => OrderErrorMessageConstant::getNotHaveErrorMessage("OFFER_ID"),
                'optionParamList.required'             => OrderErrorMessageConstant::getNotHaveErrorMessage("OPTIONPARAMLIST"),
                'optionParamList.*.option_id.required' => OrderErrorMessageConstant::getNotHaveErrorMessage("OPTION_ID"),
                'optionParamList.*.quantity.required'  => OrderErrorMessageConstant::getNotHaveErrorMessage("QUANTITY"),
                'option_price.required'                => OrderErrorMessageConstant::getNotHaveErrorMessage("OPTION_PRICE"),
                'buyer_name.required'                  => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_NAME"),
                'buyer_clearance_number.required'      => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_CLEARANCE_NUMBER"),
                'buyer_number.required'                => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_NUMBER"),
                'buyer_phone.required'                 => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_PHONE"),
                'buyer_zipcode.required'               => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_ZIPCODE"),
                'buyer_address.required'               => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_ADDRESS"),
                'buyer_memo.required'                  => OrderErrorMessageConstant::getNotHaveErrorMessage("BUYER_MEMO"),
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
