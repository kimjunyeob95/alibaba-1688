<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpConstant;
use App\Constants\ImageErrorMessageConstant;
use App\Http\Controllers\Controller;
use App\Services\GenuioService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GenuioController extends Controller
{
    private Request $request;
    private GenuioService $genuioService;

    function __construct(Request $request, GenuioService $genuioService)
    {
        $this->request       = $request;
        $this->genuioService = $genuioService;
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
            $result = $this->genuioService->tokenCreate($params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function imgTrans(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'jobId'       => 'required|string',
                'images'      => 'required|array',
                'images.*.id' => 'required|string',
            ], [
                'jobId.required'       => 'jobId를 입력하세요.',
                'images.required'      => 'images를 입력하세요.',
                'images.*.id.required' => "image id를 입력하세요.",
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $result = $this->genuioService->imgTrans($this->request->all());
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function imgTransRequest(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'offerIds'   => 'required|array',
                'offerIds.*' => 'required|string',
            ], [
                'offerIds.required' => 'offerIds를 입력하세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $result = $this->genuioService->imgTransRequest($this->request->post("offerIds"));
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function imgAiTransRequest(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'imgIds'   => 'required|array',
                'imgIds.*' => 'required|string',
            ], [
                'imgIds.required' => 'imgIds를 입력하세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $imgIds = $this->request->post("imgIds");

            $result = $this->genuioService->imgAiTransRequest($imgIds);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function imgAiRegist(int $offerId): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'images'          => 'required|array',
                'images.*.id'     => 'required|int',
                'images.*.base64' => 'required|string',
            ], [
                'images.required'          => ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGES"),
                'images.*.id.required'     => ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGES_ID"),
                'images.*.base64.required' => ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGES_BASE64")
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            
            $images = $this->request->post("images");
            $result = $this->genuioService->imgAiRegist($offerId, $images);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function imgThumnailTransRequest(int $offerId): JsonResponse
    {
        try {
            $result = $this->genuioService->imgThumnailTransRequest($offerId);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function imgDescTransRequest(int $offerId): JsonResponse
    {
        try {
            $result = $this->genuioService->imgDescTransRequest($offerId);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function queueRemove(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'queueIds' => 'required|array',
            ], [
                'queueIds.required' => 'queueIds를 입력하세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $queueIds = $this->request->post("queueIds");

            $result = $this->genuioService->queueRemove($queueIds);
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
