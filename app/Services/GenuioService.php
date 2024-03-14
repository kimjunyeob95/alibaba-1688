<?php

namespace App\Services;

use App\Abstracts\OpenApiAbstract;
use App\Constants\OpenApiConstant;
use App\Models\ApiUser;
use App\Packages\JwtPackage;
use Carbon\Carbon;
use Exception;

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
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }
}