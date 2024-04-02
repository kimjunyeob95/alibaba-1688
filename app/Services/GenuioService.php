<?php

namespace App\Services;

use App\Abstracts\TransApiAbstract;
use App\Abstracts\UploadAbstract;
use App\Constants\ImageConstant;
use App\Constants\TransApiConstant;
use App\Models\ApiUser;
use App\Models\GenuioQueueData;
use App\Models\GenuioQueueDetailData;
use App\Models\ProductData;
use App\Models\ProductImageData;
use App\Packages\JwtPackage;
use App\Vo\Genuio\QueueDto;
use Carbon\Carbon;
use Exception;
use InvalidArgumentException;
use JsonException;
use ValueError;

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
                        "id"        => $imgId,
                        "imagePath" => $product1688ImageDto->img_url_origin,
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

            if( count($payload["images"]) > 0 ){
                $apiResult = $this->apiCurl("post", "/translate-img", $payload);

                $insWhere  = [
                    "id"            => $nextId,
                    "offer_id"      => $offerId,
                    "payload_json"  => json_encode($payload, JSON_UNESCAPED_UNICODE),
                    "request_user"  => TransApiConstant::API_USER_COMPANY_OC,
                    "response_json" => json_encode($apiResult, JSON_UNESCAPED_UNICODE),
                    "created_at"    => Carbon::now()
                ];
                GenuioQueueData::insertGetId($insWhere);
    
                foreach ($queueDetailInsList as $queueDetailIns) {
                    GenuioQueueDetailData::insert($queueDetailIns);
                }
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
            $jobId  = (int)$params["jobId"];
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
                try {
                    $imgId  = (int)$image["id"];
                    $imgObj = ProductImageData::where("id", $imgId)->first();
                    if( $imgObj == null ){
                        throw new ValueError(TransApiConstant::getNotHaveErrorMessage("IMG_ID"));
                    }

                    $offerId = $imgObj->offer_id;
                    $prdObj  = ProductData::where("offer_id", $offerId)->first();
                    if( $prdObj == null ){
                        throw new ValueError(TransApiConstant::getNotHaveErrorMessage("PRODUCT"));
                    }

                    $uploadResult   = false;
                    $imgTransBase64 = "";
                    $mime           = pathinfo($imgObj->img_url_origin, PATHINFO_EXTENSION);
                    if (preg_match('/^(jpg|jpeg|png|gif)/i', $mime, $matches)) {
                        $mime = $matches[0];
                    }
                    if( $imgObj->img_type == ImageConstant::IMAGE_TYPE_MAIN ){
                        $imgName  = "/" . $this->appEnv . date('Y/m/d/') . $offerId . "_" . $imgObj->img_type . "." . $mime;
                    } else {
                        $imgName  = "/" . $this->appEnv . date('Y/m/d/') . $offerId . "_" . $imgObj->id . "_" . $imgObj->img_type . "." . $mime;
                    }
                    if( isset($image["imgTransBase64"]) && !empty($image["imgTransBase64"]) ){
                        $imgTransBase64 = $image["imgTransBase64"];
                        $uploadResult   = $this->uploadAbstract->uploadFile($imgName, base64_decode($imgTransBase64));
                    } else {
                        $fileContent = file_get_contents($imgObj->img_url_origin);
                        $status = "";
                        if( isset($image["status"]) ) {
                            $status = $image["status"];
                        }
                        $message = "";
                        if( isset($image["message"]) ) {
                            $message = $image["message"];
                        }
                        $imgTransBase64  = "status: {$status} / message: {$message}";
                        $imgEncodeBase64 = base64_encode($fileContent);
                        $uploadResult    = $this->uploadAbstract->uploadFile($imgName, base64_decode($imgEncodeBase64));
                    }

                    if( $uploadResult == true ) {
                        $img_url_trans = env("AWS_URL") . $imgName;
                        ProductImageData::where("id", $imgId)->update([
                            "img_url_trans"  => $img_url_trans,
                            "trans_dated_at" => Carbon::now(),
                        ]);

                        $prd_desc_trans = str_replace($imgObj->img_url_origin, $img_url_trans, $prdObj->prd_desc);
                        ProductData::where("offer_id", $offerId)->update([
                            "prd_desc_trans"  => $prd_desc_trans
                        ]);
                    } else {
                        ProductImageData::where("id", $imgId)->update([
                            "img_url_trans"  => "",
                            "trans_dated_at" => null,
                        ]);
                    }
    
                    GenuioQueueDetailData::where([
                        "queue_id"     => $jobId,
                        "img_id"       => $imgId,
                        "trans_status" => TransApiConstant::QUEUE_STAY,
                    ])->update([
                        "trans_status" => $uploadResult == true ? TransApiConstant::QUEUE_SUCCESS : TransApiConstant::QUEUE_FAIL,
                        "base64"       => $imgTransBase64,
                    ]);
                } catch (ValueError $ve) {
                    $errMsg = [
                        "img"   => $image,
                        "error" => $ve->getMessage()
                    ];
                    debug_log(json_encode($errMsg, JSON_UNESCAPED_UNICODE), "genuio", "genuio-img");                    
                }
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
                "response_json" => json_encode($returnMsg, JSON_UNESCAPED_UNICODE),
            ];
            $queueDto = new QueueDto();
            $queueDto->bind($bindParam);
            GenuioQueueData::create($queueDto->getAllProperties());

            // 변역 완료 여부 체크
            chkTransStatus($getGenuioObj->offer_id);
        }

        return $returnMsg;
    }

    function apiCurl(string $method, string $endPoint, array $payload = []): array
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
		} else if($method == 'POST'){
			curl_setopt_array($curl, array(
				CURLOPT_URL            => $callApi,
				CURLOPT_POST           => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_CUSTOMREQUEST  => $method,
				CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
				CURLOPT_HTTPHEADER     => $header
			));
		}
		$result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

        try {
            $apiResult = json_decode($result, JSON_UNESCAPED_UNICODE);
            if(!is_array($apiResult)) throw new InvalidArgumentException("결과가 배열이 아닙니다.");

            return $apiResult;
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