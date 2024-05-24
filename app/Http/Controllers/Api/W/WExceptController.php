<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\ExceptErrorMessageConstant;
use App\Constants\ForbiddenWordErrorMessageConstant;
use App\Constants\HttpConstant;
use App\Http\Controllers\Controller;
use App\Services\ExceptService;
use App\Services\ForbiddenWordService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WExceptController extends Controller
{
    private Request $request;
    private ExceptService $exceptService;

    function __construct(Request $request, ExceptService $exceptService)
    {
        $this->request             = $request;
        $this->exceptService = $exceptService;
    }

    public function noticeUpdate(): JsonResponse
    {
        $validator = Validator::make($this->request->all(), [
            'attribute_ids' => 'required|array',
            'is_except'     => 'required|string',
        ], [
            'attribute_ids.required' => ExceptErrorMessageConstant::getNotHaveErrorMessage("ATTRIBUTE_IDS"),
            'is_except.required'     => ExceptErrorMessageConstant::getNotHaveErrorMessage("IS_EXCEPT"),
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }

        $attribute_ids = $this->request->post("attribute_ids");
        $is_except     = $this->request->post("is_except");

        $result = $this->exceptService->noticeUpdate($attribute_ids, $is_except);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }
}
