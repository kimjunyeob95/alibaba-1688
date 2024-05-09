<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\ForbiddenWordErrorMessageConstant;
use App\Constants\HttpConstant;
use App\Http\Controllers\Controller;
use App\Services\ForbiddenWordService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WForbiddenWordController extends Controller
{
    private Request $request;
    private ForbiddenWordService $forbiddenWordService;

    function __construct(Request $request, ForbiddenWordService $forbiddenWordService)
    {
        $this->request             = $request;
        $this->forbiddenWordService = $forbiddenWordService;
    }

    public function get(int $id): JsonResponse
    {
        $result = $this->forbiddenWordService->get($id);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function create(): JsonResponse
    {
        $validator = Validator::make($this->request->all(), [
            'keyword_type'   => 'required|string',
            'target_keyword' => 'required|string',
            'apply_type'     => 'required|string',
        ], [
            'keyword_type.required'   => ForbiddenWordErrorMessageConstant::getNotHaveErrorMessage("KEYWORD_TYPE"),
            'target_keyword.required' => ForbiddenWordErrorMessageConstant::getNotHaveErrorMessage("TARGET_KEYWORD"),
            'apply_type.required'     => ForbiddenWordErrorMessageConstant::getNotHaveErrorMessage("APPLY_TYPE"),
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        $keyword_type    = $this->request->post("keyword_type");
        $target_keyword  = $this->request->post("target_keyword");
        $replace_keyword = $this->request->post("replace_keyword", "");
        $apply_type      = $this->request->post("apply_type");

        $params = [
            "keyword_type"    => $keyword_type,
            "target_keyword"  => $target_keyword,
            "replace_keyword" => $replace_keyword,
            "apply_type"      => $apply_type,
        ];

        $result = $this->forbiddenWordService->create($params);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function update(): JsonResponse
    {
        $validator = Validator::make($this->request->all(), [
            'id'             => 'required|int',
            'keyword_type'   => 'required|string',
            'target_keyword' => 'required|string',
            'apply_type'     => 'required|string',
        ], [
            'id.required'             => ForbiddenWordErrorMessageConstant::getNotHaveErrorMessage("ID"),
            'keyword_type.required'   => ForbiddenWordErrorMessageConstant::getNotHaveErrorMessage("KEYWORD_TYPE"),
            'target_keyword.required' => ForbiddenWordErrorMessageConstant::getNotHaveErrorMessage("TARGET_KEYWORD"),
            'apply_type.required'     => ForbiddenWordErrorMessageConstant::getNotHaveErrorMessage("APPLY_TYPE"),
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        $id              = $this->request->post("id");
        $keyword_type    = $this->request->post("keyword_type");
        $target_keyword  = $this->request->post("target_keyword");
        $replace_keyword = $this->request->post("replace_keyword", "");
        $apply_type      = $this->request->post("apply_type");

        $params = [
            "id"              => $id,
            "keyword_type"    => $keyword_type,
            "target_keyword"  => $target_keyword,
            "replace_keyword" => $replace_keyword,
            "apply_type"      => $apply_type,
        ];

        $result = $this->forbiddenWordService->update($params);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function delete(): JsonResponse
    {
        $validator = Validator::make($this->request->all(), [
            'ids' => 'required|array',
        ], [
            'ids.required' => ForbiddenWordErrorMessageConstant::getNotHaveErrorMessage("IDS"),
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        $result = $this->forbiddenWordService->delete($this->request->post("ids"));
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }
}
