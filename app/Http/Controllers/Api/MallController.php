<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpConstant;
use App\Constants\MallErrorMessageConstant;
use App\Constants\OnchannelConstant;
use App\Constants\OrderErrorMessageConstant;
use App\Constants\WConstant;
use App\Http\Controllers\Controller;
use App\Services\Mall\MallApiService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Process;

class MallController extends Controller
{
    private Request $request;
    private MallApiService $mallApiService;
    private string $phpAlias;

    function __construct(Request $request, MallApiService $mallApiService)
    {
        $this->request        = $request;
        $this->mallApiService = $mallApiService;
        $this->phpAlias       = env("PHP_ALIAS", "php80");
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

            $type         = $this->request->post("type", WConstant::WAPP_W1);
            $sendTypeList = $this->request->post("sendTypeList", [OnchannelConstant::PRD_CHANNEL]);

            $params       = [
                "type"         => $type,
                "sendTypeList" => $sendTypeList
            ];
            $result = $this->mallApiService->productRegist($this->request->post("offer_ids"), $params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function productLog(string $channel, int $logId)
    {
        try {
            $result = $this->mallApiService->productLog($logId);

            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function orderInfo(string $channel, string $orderId): JsonResponse
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
                'channel_order_id'            => 'required|string',
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
                'channel_order_id.required'            => OrderErrorMessageConstant::getNotHaveErrorMessage("CHANNEL_ORDER_ID"),
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
                return helpers_json_response(HttpConstant::BAD_REQUEST, $result["data"], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function imgTransRequest(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'channel_queue_id'    => 'required|int',
                'member_id'           => 'required|string',
                'images'              => 'required|array',
                'images.*.id'         => 'required|int',
                'images.*.offer_id'   => 'required|int',
                'images.*.img_id'     => 'required|int',
                'images.*.origin_url' => 'required|string',
                'images.*.img_type'   => 'required|string',
            ], [
                'channel_queue_id.required'    => MallErrorMessageConstant::getNotHaveErrorMessage("CHANNEL_QUEUE_ID"),
                'member_id.required'           => MallErrorMessageConstant::getNotHaveErrorMessage("MEMBER_ID"),
                'images.required'              => MallErrorMessageConstant::getNotHaveErrorMessage("IMAGES"),
                'images.*.id.required'         => MallErrorMessageConstant::getNotHaveErrorMessage("IMAGE_ID"),
                'images.*.offer_id.required'   => MallErrorMessageConstant::getNotHaveErrorMessage("OFFER_ID"),
                'images.*.img_id.required'     => MallErrorMessageConstant::getNotHaveErrorMessage("IMG_ID"),
                'images.*.origin_url.required' => MallErrorMessageConstant::getNotHaveErrorMessage("IMAGES_ORIGIN_URL"),
                'images.*.img_type.required'   => MallErrorMessageConstant::getNotHaveErrorMessage("IMG_TYPE"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $result = $this->mallApiService->imgTransRequest($this->request->all());
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
                'jobId'               => 'required|string',
                'images'              => 'required|array',
                'images.*.id'         => 'required|string',
                'images.*.origin_url' => 'required|string',
            ], [
                'jobId.required'               => MallErrorMessageConstant::getNotHaveErrorMessage("JOB_ID"),
                'images.required'              => MallErrorMessageConstant::getNotHaveErrorMessage("IMAGES"),
                'images.*.id.required'         => MallErrorMessageConstant::getNotHaveErrorMessage("IMAGE_ID"),
                'images.*.origin_url.required' => MallErrorMessageConstant::getNotHaveErrorMessage("IMAGES_ORIGIN_URL"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $result = $this->mallApiService->imgTrans($this->request->all());
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function imgUpload(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'member_id'               => 'required|string',
                'images'                  => 'required|array',
                'images.*.id'             => 'required|int',
                'images.*.base64'         => 'required|string',
            ], [
                'member_id.required'               => MallErrorMessageConstant::getNotHaveErrorMessage("MEMBER_ID"),
                'images.required'                  => MallErrorMessageConstant::getNotHaveErrorMessage("IMAGES"),
                'images.*.id.required'             => MallErrorMessageConstant::getNotHaveErrorMessage("IMAGE_ID"),
                'images.*.base64.required'         => MallErrorMessageConstant::getNotHaveErrorMessage("IMAGE_BASE64"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $result = $this->mallApiService->imgUpload($this->request->all());
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function allProductRegist(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'offerIds' => 'required|array',
            ], [
                'offerIds.required' => MallErrorMessageConstant::getNotHaveErrorMessage("OFFERIDS"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $offerIds     = $this->request->post("offerIds");
            $offerIds     = array_unique($offerIds);
            $es_send_type = $this->request->post("es_send_type", []);
            $oc_send_type = $this->request->post("oc_send_type", []);

            if( !empty($es_send_type) ){
                $options = "--offerids=" . helperEscape(implode(",", $offerIds)) . " --type=" . helperEscape(implode(",", $es_send_type));

                $command = "nohup " . $this->phpAlias . " artisan easy_sell_command --func=productRegist " . $options . " > /dev/null 2>&1 &";
                $process1 = Process::fromShellCommandline($command);
                $process1->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
                $process1->setTimeout(null); // 실행 시간 제한 없음
                $process1->start();
            }

            if( !empty($oc_send_type) ){
                $options = "--offerids=" . helperEscape(implode(",", $offerIds)) . " --sendtype=" . helperEscape(implode(",", $oc_send_type));

                $command = "nohup " . $this->phpAlias . " artisan onchannel_command --func=productRegist " . $options . " > /dev/null 2>&1 &";
                $process2 = Process::fromShellCommandline($command);
                $process2->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
                $process2->setTimeout(null); // 실행 시간 제한 없음
                $process2->start();
            }

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "전송 요청 완료되었습니다.\n전송 내역은 상품 전송 현황 페이지에서 확인이 가능합니다."));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function productRegistLog(int $offerId): JsonResponse
    {
        try {
            $result = $this->mallApiService->productRegistLog($offerId);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function productWappRegist(string $channel, int $offerId): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'channel_type' => 'required|string',
            ], [
                'channel_type.required' => MallErrorMessageConstant::getNotHaveErrorMessage("CHANNEL_TYPE"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $channelType = $this->request->post("channel_type");
            $params       = [
                "channel_type" => $channelType,
            ];

            $result = $this->mallApiService->productWappRegist($offerId, $params);
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
