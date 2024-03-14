<?php

namespace App\Http\Controllers;

use App\Abstracts\OpenApiAbstract;
use App\Constants\HttpConstant;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GenuioController extends Controller
{
    private Request $request;
    private OpenApiAbstract $openApiAbstract;

    function __construct(Request $request, OpenApiAbstract $openApiAbstract)
    {
        $this->request         = $request;
        $this->openApiAbstract = $openApiAbstract;
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
            $result = $this->openApiAbstract->tokenCreate($params);
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
                'jobId'                   => 'required|int',
                'images'                  => 'required|array',
                'images.*.id'             => 'required|int',
                'images.*.imgTransBase64' => 'required|string',
            ], [
                'jobId.required'                   => 'jobId를 입력하세요.',
                'images.required'                  => 'images를 입력하세요.',
                'images.*.id.required'             => "image id를 입력하세요.",
                'images.*.imgTransBase64.required' => "image imgTransBase64를 입력하세요.",
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $result = $this->openApiAbstract->imgTrans($this->request->all());
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
