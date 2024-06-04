<?php

namespace App\Traits\Genuio;

use App\Constants\GenuioConstant;
use App\Constants\GenuioErrorMessageConstant;
use App\Constants\TransApiConstant;
use App\Models\GenuioQueueData;
use Exception;

trait QueueTrait
{
    protected array $returnMsg;
    private string $domain;
    private string $token;
    
    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
        $this->domain    = env("GENUIO_DOMAIN");
        $this->token     = env("GENUIO_TOKEN");
    }

    /**
     * @func queueRemove
     * @description 'SAI 큐 삭제'
     * @param array $queueIds
     * @return array
    */
    public function queueRemove(array $queueIds): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $fails = [];
            
            foreach ($queueIds as $queueId) {
                try {
                    $geObj = GenuioQueueData::where([
                        "id"           => $queueId,
                        "request_user" => TransApiConstant::API_USER_COMPANY_OC
                    ])->first();
    
                    if( $geObj != null ){
                        $childObj = GenuioQueueData::where([
                            "parent_id"    => $queueId,
                            "request_user" => TransApiConstant::API_USER_COMPANY_GENUIO
                        ])->first();
    
                        if( $childObj != null ){
                            throw new Exception(GenuioErrorMessageConstant::getFitErrorMessage("QUEUE_DONE"));
                        }
    
                        $resJson   = $geObj->response_json;
                        $resDecode = json_decode($resJson, JSON_UNESCAPED_UNICODE);
                        
                        if( isset($resDecode["data"]["internalJobId"]) ){
                            $internalJobId = $resDecode["data"]["internalJobId"];
                            $endPoint      = $this->domain . "/translate-progress/remove/{$internalJobId}?queue=priority";
                            $header        = ["Authorization: Bearer {$this->token}"];
                            $result        = helpers_curl("POST", $endPoint, $header);
                            if( $result["status"] == GenuioConstant::REMOVE_QUEUE_OK ){
                                GenuioQueueData::where("id", $geObj->id)->delete();
                            } else {
                                throw new Exception(GenuioErrorMessageConstant::getFitErrorMessage("QUEUE_REMOVE_ERROR"));
                            }
                        } else {
                            throw new Exception(GenuioErrorMessageConstant::getFitErrorMessage("INTERNALJOBID"));
                        }
                    } else {
                        throw new Exception(GenuioErrorMessageConstant::getFitErrorMessage("QUEUE"));
                    }
                } catch (Exception $de) {
                    $fails[] = [
                        "queueId" => $queueId,
                        "msg"     => $de->getMessage()
                    ];
                }
            }

            $returnMsg = helpers_success_message(["fails" => $fails]);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }
        return $returnMsg;
    }
}
