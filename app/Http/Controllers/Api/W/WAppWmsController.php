<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\HttpConstant;
use App\Constants\WmsConstant;
use App\Constants\WmsErrorMessageConstant;
use App\Http\Controllers\Controller;
use App\Services\Wms\WmsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Validator;

class WAppWmsController extends Controller
{
    private Request $request;
    private WmsService $wmsService;
    private string $phpAlias;

    function __construct(Request $request, WmsService $wmsService)
    {
        $this->request    = $request;
        $this->wmsService = $wmsService;
        $this->phpAlias   = env("PHP_ALIAS", "php80");
    }

    public function apiHsCodeList(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'begin_page' => 'required|int',
                'page_size'  => 'required|int',
            ], [
                'begin_page.required' => WmsErrorMessageConstant::getNotHaveErrorMessage("BEGIN_PAGE"),
                'page_size.required'  => WmsErrorMessageConstant::getNotHaveErrorMessage("PAGE_SIZE"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $page       = $this->request->get("begin_page", 1);
            $pageSize   = $this->request->get("page_size", 50);
            $pageSize   = $pageSize > 500 ? 500 : $pageSize;
            $search_cls = $this->request->get("search_cls", WmsConstant::HSCODE_SEARCH_TYPE_KO);
            $keyword    = $this->request->get("keyword", "");
            $sort       = $this->request->get("sort", "property_code_name|desc");

            $params = [
                "page"       => $page,
                "pageSize"   => $pageSize,
                "search_cls" => $search_cls,
                "keyword"    => $keyword,
                "sort"       => $sort,
            ];
            $result  = $this->wmsService->apiHsCodeList($params);

            return helpers_json_response(HttpConstant::OK, $result);
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function getTariff(string $hsCode): JsonResponse
    {
        try {
            $result = $this->wmsService->getTariff($hsCode);

            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function apiTarffi(string $channel, string $hsCode): JsonResponse
    {
        try {
            $result = $this->wmsService->getTariff($hsCode);

            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function bonaeraInHscodeUpdate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'ids'     => 'required|array',
                'hs_code' => 'required|string',
            ], [
                'ids.required'     => WmsErrorMessageConstant::getNotHaveErrorMessage("IDS"),
                'hs_code.required' => WmsErrorMessageConstant::getNotHaveErrorMessage("HS_CODE"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $ids    = $this->request->post("ids");
            $hsCode = $this->request->post("hs_code");
            $result = $this->wmsService->bonaeraInHscodeUpdate($ids, $hsCode);

            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function bonaeraInFailHscodeUpdate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'ids'     => 'required|array',
                'hs_code' => 'required|string',
            ], [
                'ids.required'     => WmsErrorMessageConstant::getNotHaveErrorMessage("IDS"),
                'hs_code.required' => WmsErrorMessageConstant::getNotHaveErrorMessage("HS_CODE"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $ids    = $this->request->post("ids");
            $hsCode = $this->request->post("hs_code");
            $result = $this->wmsService->bonaeraInFailHscodeUpdate($ids, $hsCode);

            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function bonaeraInFailCreate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'ids' => 'required|array',
            ], [
                'ids.required' => 'ids를 입력하세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $ids = $this->request->post("ids");
            $options  = "--func=bonaeraInFailCreate --ids=" . helperEscape(implode(",", $ids));
            $command  = "nohup " . $this->phpAlias . " artisan wms_command " . $options . " > /dev/null 2>&1 &";
            $process  = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "입고신청 요청 완료\r\n처리 건이 많을 경우 업데이트에 시간이 소요될 수 있습니다."));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function bonaeraInUpdate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'ids' => 'required|array',
            ], [
                'ids.required' => 'ids를 입력하세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $ids = $this->request->post("ids");
            $options  = "--func=bonaeraInUpdate --ids=" . helperEscape(implode(",", $ids));
            $command  = "nohup " . $this->phpAlias . " artisan wms_command " . $options . " > /dev/null 2>&1 &";
            $process  = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "입고정보 업데이트 요청 완료\r\n처리 건이 많을 경우 업데이트에 시간이 소요될 수 있습니다."));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function bonaeraOutCreate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'ids' => 'required|array',
            ], [
                'ids.required' => 'ids를 입력하세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $ids = $this->request->post("ids");
            $options  = "--func=bonaeraOutCreate --ids=" . helperEscape(implode(",", $ids));
            $command  = "nohup " . $this->phpAlias . " artisan wms_command " . $options . " > /dev/null 2>&1 &";
            $process  = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "출고신청 완료\r\n처리 건이 많을 경우 업데이트에 시간이 소요될 수 있습니다."));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function bonaeraOutUpdate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'ids' => 'required|array',
            ], [
                'ids.required' => 'ids를 입력하세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $ids = $this->request->post("ids");
            $options  = "--func=bonaeraOutUpdate --ids=" . helperEscape(implode(",", $ids));
            $command  = "nohup " . $this->phpAlias . " artisan wms_command " . $options . " > /dev/null 2>&1 &";
            $process  = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "출고정보 업데이트 요청 완료\r\n처리 건이 많을 경우 업데이트에 시간이 소요될 수 있습니다."));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function bonaeraOutPay(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'ids' => 'required|array',
            ], [
                'ids.required' => 'ids를 입력하세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $ids = $this->request->post("ids");
            $options  = "--func=bonaeraOutPay --ids=" . helperEscape(implode(",", $ids));
            $command  = "nohup " . $this->phpAlias . " artisan wms_command " . $options . " > /dev/null 2>&1 &";
            $process  = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "출고 배송비 결제 요청 완료\r\n처리 건이 많을 경우 업데이트에 시간이 소요될 수 있습니다."));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }
}
