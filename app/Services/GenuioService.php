<?php

namespace App\Services;

use App\Abstracts\OpenApiAbstract;
use App\Constants\OpenApiConstant;
use App\Models\ApiUser;
use App\Models\GenuioQueueData;
use App\Models\GenuioQueueDetailData;
use App\Packages\JwtPackage;
use App\Vo\Product\QueueDto;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\ValidationException;

class GenuioService extends OpenApiAbstract
{
    private JwtPackage $jwtPackage;

    public function __construct(JwtPackage $jwtPackage)
    {
        parent::__construct(OpenApiConstant::API_USER_COMPANY_GENUIO);
        $this->jwtPackage = $jwtPackage;
    }
    
    /**
     * @func tokenCreate
     * @description '토큰 생성'
     */
    public function tokenCreate(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $userId = $params["user_id"];
            
            $result = $this->jwtPackage->tokenCreate($userId, $this->user_company);
            if( $result["isSuccess"] && isset($result["data"]["token"]) ){
                ApiUser::where([
                    'user_id'      => $userId,
                    'user_company' => $this->user_company,
                ])->update([
                    "updated_at" => Carbon::now()
                ]);
                $tokenResult = $result["data"];
                $returnMsg   = helpers_success_message($tokenResult);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func imgTrans
     * @description '번역된 이미지 처리'
     */
    public function imgTrans(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $jobId  = $params["jobId"];
            $images = $params["images"];

            $getGenuioObj = GenuioQueueData::where([
                "id"           => $jobId,
                "request_user" => OpenApiConstant::API_USER_COMPANY_OC
            ])->first();
            if( $getGenuioObj == null ) {
                throw new Exception(OpenApiConstant::getNotHaveErrorMessage("QUEUE_ID"));
            }

            $getGenuioDetailObjs = GenuioQueueDetailData::where("queue_id", $jobId)->where("base64", "")->get();
            if( count($images) != count($getGenuioDetailObjs) ){
                throw new ValidationException(OpenApiConstant::getFitErrorMessage("NOT_EQUAL_COUNT_IMAGE"));
            }

            foreach ($images as $image) {
                
            }

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        } catch (ValidationException $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        $bindParam = [
            "offerId"       => $getGenuioObj->offer_id,
            "payload_json"  => "", // base64가 너무 길어 그냥 ""처리
            "request_user"  => OpenApiConstant::API_USER_COMPANY_GENUIO,
            "response_json" => $returnMsg["msg"],
        ];
        $queueDto = new QueueDto();
        $queueDto->bind($bindParam);

        GenuioQueueData::create($queueDto->getAllProperties());

        return $returnMsg;
    }
}