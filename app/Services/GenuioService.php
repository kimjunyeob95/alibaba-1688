<?php

namespace App\Services;

use App\Abstracts\TransApiAbstract;
use App\Abstracts\UploadAbstract;
use App\Constants\ImageConstant;
use App\Constants\TransApiConstant;
use App\Models\ApiUser;
use App\Models\GenuioQueueData;
use App\Models\GenuioQueueDetailData;
use App\Models\ProductImageData;
use App\Packages\JwtPackage;
use App\Vo\Genuio\QueueDto;
use Carbon\Carbon;
use Exception;
use InvalidArgumentException;
use JsonException;

class GenuioService extends TransApiAbstract
{
    private string $appEnv;
    private JwtPackage $jwtPackage;
    private UploadAbstract $uploadAbstract;
    private string $domain;
    private string $token;
    protected array $returnMsg;

    public function __construct(
        JwtPackage $jwtPackage,
        UploadAbstract $uploadAbstract
    )
    {
        parent::__construct(TransApiConstant::API_USER_COMPANY_GENUIO);
        $this->appEnv         = ( env("APP_ENV", "local") != "production" ) ? "dev/" : "";
        $this->jwtPackage     = $jwtPackage;
        $this->uploadAbstract = $uploadAbstract;
        $this->domain         = env("GENUIO_DOMAIN");
        $this->token          = env("GENUIO_TOKEN");
        $this->returnMsg      = helpers_fail_message();
    }

    public function translateImage(string $imgPath): array
    {
        $endPoint = "/translate-img?url={$imgPath}";
        return $this->apiCurl("GET", $endPoint);
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
     * @func createTransProductImg
     * @description '이미지 번역 통신'
     * @param array $product1688ImageDtoList
     * @param int $offerId
     */
    public function createTransProductImg(array $product1688ImageDtoList, int $offerId): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $lastId = GenuioQueueData::max('id');
            $nextId = $lastId + 1;

            $payload = [
                "jobId"  => $nextId,
                "images" => []
            ];

            $queueDetailInsList = [];
            foreach ($product1688ImageDtoList as $product1688ImageDto) {
                if( $product1688ImageDto->is_change_img == true ){
                    $imgId = ProductImageData::where([
                        "offer_id"       => $product1688ImageDto->offer_id,
                        "img_type"       => $product1688ImageDto->img_type,
                        "img_url_origin" => $product1688ImageDto->img_url_origin,
                    ])->value('id');
                    $payload["images"][] = [
                        "id"      => $imgId,
                        "imgPath" => $product1688ImageDto->img_url_origin,
                    ];

                    $queueDetailInsList[] = [
                        "queue_id"     => $nextId,
                        "img_id"       => $imgId,
                        "trans_status" => TransApiConstant::QUEUE_STAY,
                        "base64"       => "",
                        "created_at"   => Carbon::now()
                    ];
                }
            }

            $insWhere = [
                "id"            => $nextId,
                "offer_id"      => $offerId,
                "payload_json"  => json_encode($payload, JSON_UNESCAPED_UNICODE),
                "request_user"  => TransApiConstant::API_USER_COMPANY_OC,
                "response_json" => "",
                "created_at"    => Carbon::now()
            ];
            GenuioQueueData::insertGetId($insWhere);

            foreach ($queueDetailInsList as $queueDetailIns) {
                GenuioQueueDetailData::insert($queueDetailIns);
            }

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $errorMsg  = "offerId: {$offerId} | errorTitle: " . TransApiConstant::getFitErrorMessage("TRANS_REQUEST_IMAGE") . "errorDesc: " . $e->getMessage();
            $returnMsg = helpers_fail_message(false, $errorMsg);
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
                "request_user" => TransApiConstant::API_USER_COMPANY_OC
            ])->first();
            if( $getGenuioObj == null ) {
                throw new Exception(TransApiConstant::getNotHaveErrorMessage("QUEUE_ID"));
            }

            $getGenuioDetailObjs = GenuioQueueDetailData::where("queue_id", $jobId)
            ->where("trans_status", TransApiConstant::QUEUE_STAY)
            ->where("base64", "")
            ->get();
            if( count($images) != count($getGenuioDetailObjs) ){
                throw new Exception(TransApiConstant::getFitErrorMessage("NOT_EQUAL_COUNT_IMAGE"));
            }

            foreach ($images as $image) {
                $imgObj       = ProductImageData::where("id", $image["id"])->first();
                $uploadResult = false;
                if( $imgObj != null ){
                    $mime = pathinfo($imgObj->img_url_origin, PATHINFO_EXTENSION);
                    if( $imgObj->img_type == ImageConstant::IMAGE_TYPE_MAIN ){
                        $imgName  = "/" . $this->appEnv . date('Y/m/d/') . $imgObj->offer_id . "_" . $imgObj->img_type . "." . $mime;
                    } else {
                        $imgName  = "/" . $this->appEnv . date('Y/m/d/') . $imgObj->offer_id . "_" . $imgObj->id . "_" . $imgObj->img_type . "." . $mime;
                    }
                    $uploadResult = $this->uploadAbstract->uploadFile($imgName, base64_decode($image["imgTransBase64"]));
                    if( $uploadResult == true ) {
                        ProductImageData::where("id", $image["id"])->update([
                            "img_url_trans"  => env("AWS_URL") . $imgName,
                            "trans_dated_at" => Carbon::now(),
                        ]);
                    }
                }

                GenuioQueueDetailData::where([
                    "queue_id"     => $jobId,
                    "img_id"       => $image["id"],
                    "trans_status" => TransApiConstant::QUEUE_STAY,
                ])->update([
                    "trans_status" => $uploadResult == true ? TransApiConstant::QUEUE_SUCCESS : TransApiConstant::QUEUE_FAIL,
                    "base64"       => $image["imgTransBase64"],
                ]);
            }
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        if( $getGenuioObj != null ){
            $bindParam = [
                "offerId"       => $getGenuioObj->offer_id,
                "payload_json"  => "", // base64가 너무 길어 그냥 ""처리
                "request_user"  => TransApiConstant::API_USER_COMPANY_GENUIO,
                "response_json" => json_encode($returnMsg["msg"], JSON_UNESCAPED_UNICODE),
            ];
            $queueDto = new QueueDto();
            $queueDto->bind($bindParam);
            GenuioQueueData::create($queueDto->getAllProperties());
        }

        return $returnMsg;
    }

    function apiCurl(string $method, string $endPoint): array
    {
		$returnMsg = $this->returnMsg;
		$callApi   = $this->domain . $endPoint;
		$curl      = curl_init();
		$method    = strtoupper($method);

        $header = ["Authorization: Bearer {$this->token}"];

		if($method == 'GET') {
			curl_setopt_array($curl, array(
				CURLOPT_URL            => $callApi,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_TIMEOUT        => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST  => $method,
				CURLOPT_HTTPHEADER     => $header
			));
		}
		$result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

        try {
            $apiResult = json_decode($result, JSON_UNESCAPED_UNICODE);
            if(!is_array($apiResult)) throw new InvalidArgumentException("결과가 배열이 아닙니다.");

            $returnMsg = helpers_success_message($apiResult);
        } catch (JsonException $e) {
            $returnMsg = helpers_fail_message(false, "결과가 Json이 아닙니다.");
        } catch (InvalidArgumentException $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }
}