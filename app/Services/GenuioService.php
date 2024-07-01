<?php

namespace App\Services;

use App\Abstracts\TransApiAbstract;
use App\Abstracts\UploadAbstract;
use App\Constants\EasySellConstant;
use App\Constants\GenuioConstant;
use App\Constants\ImageConstant;
use App\Constants\ImageErrorMessageConstant;
use App\Constants\MallConstant;
use App\Constants\OnchannelConstant;
use App\Constants\TransApiConstant;
use App\Constants\WConstant;
use App\Models\ApiUser;
use App\Models\EasysellProductLog;
use App\Models\GenuioAiData;
use App\Models\GenuioImageData;
use App\Models\GenuioQueueData;
use App\Models\GenuioQueueDetailData;
use App\Models\OcGeQueueData;
use App\Models\ProductData;
use App\Models\ProductImageData;
use App\Packages\JwtPackage;
use App\Vo\Genuio\QueueDto;
use App\Vo\Product\Product1688ImageDto;
use Carbon\Carbon;
use Exception;
use InvalidArgumentException;
use JsonException;
use ValueError;
use Symfony\Component\Process\Process;

class GenuioService extends TransApiAbstract
{
    private JwtPackage $jwtPackage;
    private UploadAbstract $uploadAbstract;
    protected string $domain;
    protected string $token;
    protected array $returnMsg;
    private string $phpAlias;

    public function __construct(
        JwtPackage $jwtPackage,
        UploadAbstract $uploadAbstract
    )
    {
        parent::__construct(TransApiConstant::API_USER_COMPANY_GENUIO);
        $this->jwtPackage     = $jwtPackage;
        $this->uploadAbstract = $uploadAbstract;
        $this->domain         = env("GENUIO_DOMAIN");
        $this->token          = env("GENUIO_TOKEN");
        $this->returnMsg      = helpers_fail_message();
        $this->phpAlias       = env("PHP_ALIAS", "php80");
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
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func createTransProductImg
     * @description '이미지 번역 통신'
     * @param array $product1688ImageDtoList
     * @param int $offerId
     * @param bool $priority
     * @param array $params
     * @return array
     */
    public function createTransProductImg(array $product1688ImageDtoList, int $offerId, bool $priority = GenuioConstant::PRIORITY_FALSE, array $params = []): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $insWhere  = [
                "offer_id"      => $offerId,
                "parent_id"     => 0,
                "send_type"     => GenuioConstant::IMG_TRANS,
                "payload_json"  => "",
                "request_user"  => TransApiConstant::API_USER_COMPANY_OC,
                "response_json" => "",
                "created_at"    => Carbon::now()
            ];
            $nextId = GenuioQueueData::insertGetId($insWhere);

            $prdObj = ProductData::with([
                "options",
                "notices",
            ])->where("offer_id", $offerId)->first();

            $payload = [
                "jobId"  => $nextId,
                "images" => [],
                "prdObj" => $prdObj->toArray(),
                "params" => $params
            ];

            $queueDetailInsList = [];
            $imgIds             = [];
            foreach ($product1688ImageDtoList as $product1688ImageDto) {
                if( $product1688ImageDto->is_change_img == true && $product1688ImageDto->lang == WConstant::WAPP_KR ){
                    $imgObj = ProductImageData::where([
                        "offer_id"       => $product1688ImageDto->offer_id,
                        "img_type"       => $product1688ImageDto->img_type,
                        "lang"           => $product1688ImageDto->lang,
                        "img_url_origin" => $product1688ImageDto->img_url_origin,
                    ])->whereNotIn("id", $imgIds)->first();

                    if( $imgObj != null ){
                        $isThumbnail = false;
                        if( $imgObj->img_type != ImageConstant::IMAGE_TYPE_DESC ){
                            $isThumbnail = true;
                        }
                        $payload["images"][] = [
                            "id"          => $imgObj->id,
                            "imagePath"   => $product1688ImageDto->img_url_origin,
                            "isThumbnail" => $isThumbnail,
                            "priority"    => $priority,
                            "img_type"    => $imgObj->img_type,
                            "lang"        => $imgObj->lang,
                        ];
    
                        $queueDetailInsList[] = [
                            "queue_id"     => $nextId,
                            "img_id"       => $imgObj->id,
                            "trans_status" => TransApiConstant::QUEUE_STAY,
                            "base64"       => "",
                            "created_at"   => Carbon::now()
                        ];
    
                        $imgIds[] = $imgObj->id;
                    }
                    
                }
            }

            if( count($payload["images"]) > 0 ){
                $apiResult = $this->apiCurl("post", "/translate-img", $payload);

                $upWhere = [];
                $upWhere["payload_json"] = json_encode($payload, JSON_UNESCAPED_UNICODE);

                if( $apiResult["isSuccess"] == true ){
                    $upWhere["response_json"] = json_encode($apiResult["data"], JSON_UNESCAPED_UNICODE);
                } else {
                    $upWhere["response_json"] = json_encode($apiResult, JSON_UNESCAPED_UNICODE);
                }
                GenuioQueueData::where("id", $nextId)->update($upWhere);
    
                foreach ($queueDetailInsList as $queueDetailIns) {
                    GenuioQueueDetailData::insert($queueDetailIns);
                }
            }

            if( $apiResult["isSuccess"] == true ){
                chkTransStatus($offerId);

                $returnMsg = helpers_success_message();
            } else {
                $returnMsg = helpers_fail_message($apiResult["msg"]);
            }

        } catch (Exception $e) {
            $errorMsg  = "offerId: {$offerId} | errorTitle: " . TransApiConstant::getFitErrorMessage("TRANS_REQUEST_IMAGE") . "errorDesc: " . $e->getMessage();
            $returnMsg = helpers_fail_message($errorMsg);
        }

        return $returnMsg;
    }

    /**
     * @func createTransProductImgAgain
     * @description '이미지 재번역 통신'
     * @param array $product1688ImageDtoList
     * @param int $offerId
     * @param bool $priority
     * @param array $params
     * @return array
     */
    public function createTransProductImgAgain(array $product1688ImageDtoList, int $offerId, bool $priority = GenuioConstant::PRIORITY_FALSE, array $params = []): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $insWhere  = [
                "offer_id"      => $offerId,
                "parent_id"     => 0,
                "send_type"     => GenuioConstant::IMG_TRANS_AGAIN,
                "payload_json"  => "",
                "request_user"  => TransApiConstant::API_USER_COMPANY_OC,
                "response_json" => "",
                "created_at"    => Carbon::now()
            ];
            $nextId = GenuioQueueData::insertGetId($insWhere);

            $prdObj = ProductData::with([
                "options",
                "notices",
            ])->where("offer_id", $offerId)->first();

            $payload = [
                "jobId"  => $nextId,
                "images" => [],
                "prdObj" => $prdObj->toArray(),
                "params" => $params
            ];

            $queueDetailInsList = [];
            $imgIds             = [];
            foreach ($product1688ImageDtoList as $product1688ImageDto) {
                if( $product1688ImageDto->is_change_img == true && $product1688ImageDto->lang == WConstant::WAPP_KR ){
                    $imgObj = ProductImageData::where([
                        "offer_id"       => $product1688ImageDto->offer_id,
                        "img_type"       => $product1688ImageDto->img_type,
                        "lang"           => $product1688ImageDto->lang,
                        "img_url_origin" => $product1688ImageDto->img_url_origin,
                    ])->whereNotIn("id", $imgIds)->first();

                    if( $imgObj != null ){
                        $isThumbnail = false;
                        if( $imgObj->img_type != ImageConstant::IMAGE_TYPE_DESC ){
                            $isThumbnail = true;
                        }
                        $payload["images"][] = [
                            "id"          => $imgObj->id,
                            "imagePath"   => $product1688ImageDto->img_url_origin,
                            "isThumbnail" => $isThumbnail,
                            "priority"    => $priority,
                            "img_type"    => $imgObj->img_type,
                            "lang"        => $imgObj->lang,
                        ];
    
                        $queueDetailInsList[] = [
                            "queue_id"     => $nextId,
                            "img_id"       => $imgObj->id,
                            "trans_status" => TransApiConstant::QUEUE_STAY,
                            "base64"       => "",
                            "created_at"   => Carbon::now()
                        ];
    
                        $imgIds[] = $imgObj->id;
                    }
                }
            }

            if( count($payload["images"]) > 0 ){
                $apiResult = $this->apiCurl("post", "/translate-img", $payload);

                $upWhere = [];
                $upWhere["payload_json"] = json_encode($payload, JSON_UNESCAPED_UNICODE);

                if( $apiResult["isSuccess"] == true ){
                    $upWhere["response_json"] = json_encode($apiResult["data"], JSON_UNESCAPED_UNICODE);
                } else {
                    $upWhere["response_json"] = json_encode($apiResult, JSON_UNESCAPED_UNICODE);
                }
                GenuioQueueData::where("id", $nextId)->update($upWhere);
    
                foreach ($queueDetailInsList as $queueDetailIns) {
                    GenuioQueueDetailData::insert($queueDetailIns);
                }
            }

            if( $apiResult["isSuccess"] == true ){
                chkTransStatus($offerId);

                $returnMsg = helpers_success_message();
            } else {
                $returnMsg = helpers_fail_message($apiResult["msg"]);
            }

        } catch (Exception $e) {
            $errorMsg  = "offerId: {$offerId} | errorTitle: " . TransApiConstant::getFitErrorMessage("TRANS_REQUEST_IMAGE") . "errorDesc: " . $e->getMessage();
            $returnMsg = helpers_fail_message($errorMsg);
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
            $jobId     = (int)$params["jobId"];
            $images    = $params["images"];

            $getGenuioObj = GenuioQueueData::where([
                "id"           => $jobId,
                "request_user" => TransApiConstant::API_USER_COMPANY_OC,
                "parent_id"    => "0"
            ])->first();
            if( $getGenuioObj == null ) {
                throw new Exception(TransApiConstant::getNotHaveErrorMessage("JOB_ID"));
            }

            $payloadJson = json_decode($getGenuioObj->payload_json, JSON_UNESCAPED_UNICODE);
            $detailCnt   = count($payloadJson["images"]);

            if( count($images) != $detailCnt ){
                throw new Exception(TransApiConstant::getFitErrorMessage("NOT_EQUAL_COUNT_IMAGE"));
            }

            $childObj = GenuioQueueData::where([
                "parent_id"    => $jobId,
                "request_user" => TransApiConstant::API_USER_COMPANY_GENUIO
            ])->first();
            if( $childObj != null ){
                throw new Exception(TransApiConstant::getFitErrorMessage("ALREADY_QUEUE"));
            }

            $offerId = $getGenuioObj->offer_id;

            $prdObj = ProductData::where("offer_id", $offerId)->first();
            if( $prdObj == null ){
                throw new ValueError(TransApiConstant::getNotHaveErrorMessage("PRODUCT"));
            }

            // debug_log(json_encode($params, JSON_UNESCAPED_UNICODE), "genuio/imgTrans", "imgTrans");

            if( $getGenuioObj->send_type == GenuioConstant::IMG_TRANS ){

                $dateName = $prdObj->created_at->format('Y/m/d');

                // 1. product_image_datas update
                foreach ($images as $image) {
                    try {
                        $imgId     = (int)$image["id"];
                        $is_except = ImageConstant::IS_EXCEPT_N;
                        if( isset($image["excluded"]) && $image["excluded"] === true ) {
                            $is_except = ImageConstant::IS_EXCEPT_Y;
                        }

                        $imgObj = ProductImageData::where("id", $imgId)->first();
                        if( $imgObj == null ){
                            throw new ValueError(TransApiConstant::getNotHaveErrorMessage("IMG_ID"));
                        }

                        if( $is_except == ImageConstant::IS_EXCEPT_Y || $imgObj->is_except == ImageConstant::IS_EXCEPT_Y ){
                            ProductImageData::where("id", $imgId)->update([
                                "is_except" => ImageConstant::IS_EXCEPT_Y
                            ]);
                            throw new ValueError(ImageErrorMessageConstant::getFitErrorMessage("EXCEPT_IMG"));
                        }
    
                        $img_url_origin = $imgObj->img_url_origin;
    
                        $uploadResult   = false;
                        $errorImgFlag   = false;
                        $imgTransBase64 = "";
                        $img_url_trans  = "";
                        $mime           = pathinfo($img_url_origin, PATHINFO_EXTENSION);
                        if (preg_match('/^(jpg|jpeg|png|gif)/i', $mime, $matches)) {
                            $mime = $matches[0];
                        }
                        if( $imgObj->img_type == ImageConstant::IMAGE_TYPE_MAIN ){
                            $imgName  = "/product/" . $dateName . "/" . $offerId . "_" . $imgObj->img_type . "." . $mime;
                        } else {
                            $imgName  = "/product/" . $dateName . "/" . $offerId . "_" . $imgObj->id . "_" . $imgObj->img_type . "." . $mime;
                        }
                        if( isset($image["imgTransBase64"]) && !empty($image["imgTransBase64"]) ){
                            $imgTransBase64 = $image["imgTransBase64"];
                            $uploadResult   = $this->uploadAbstract->uploadFile($imgName, base64_decode($imgTransBase64));
                        } else {
                            try {
                                $fileContent = fileContents($img_url_origin);
                                $status      = "";
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
                            } catch (Exception $th) {
                                $errorImgFlag   = true;
                                $imgTransBase64 = TransApiConstant::getFitErrorMessage("1688_IMG");
                            }
                        }
    
                        if( $uploadResult == true ) {
                            $img_url_trans = env("AWS_URL") . $imgName;
                            ProductImageData::where("id", $imgId)->update([
                                "img_url_trans"  => $img_url_trans,
                                "trans_dated_at" => Carbon::now(),
                            ]);

                            // 1. origin 이미지
                            GenuioImageData::updateOrCreate([
                                "offer_id"  => $offerId,
                                "img_id"    => $imgObj->id,
                                "ai_type"   => GenuioConstant::IMG_Ai_TRANS,
                                "is_origin" => GenuioConstant::IS_ORIGIN_Y,
                            ],[
                                "img_url_ai" => $img_url_origin,
                            ]);

                            // 2. 최초 번역 이미지
                            $aiImgObj = GenuioImageData::where([
                                "offer_id"   => $offerId,
                                "img_id"     => $imgObj->id,
                                "img_url_ai" => $img_url_trans,
                                "ai_type"    => GenuioConstant::IMG_Ai_TRANS,
                                "is_origin"  => GenuioConstant::IS_ORIGIN_N
                            ])->first();
                            if( $aiImgObj == null ){
                                GenuioImageData::create([
                                    "offer_id"   => $offerId,
                                    "img_id"     => $imgObj->id,
                                    "img_url_ai" => $img_url_trans,
                                    "ai_type"    => GenuioConstant::IMG_Ai_TRANS,
                                    "is_origin"  => GenuioConstant::IS_ORIGIN_N,
                                ]);
                            } else {
                                GenuioImageData::where("id", $aiImgObj->id)->update([
                                    "updated_at" => Carbon::now()
                                ]);
                            }
                        } else {
                            /** 어떠한 이유로 S3로 업로드 실패 시 제외처리 */
                            ProductImageData::where("id", $imgId)->update([
                                "is_except"      => ImageConstant::IS_EXCEPT_Y,
                                "img_url_trans"  => $img_url_trans,
                                "trans_dated_at" => null,
                            ]);
                        }
        
                        GenuioQueueDetailData::where([
                            "queue_id"     => $jobId,
                            "img_id"       => $imgId,
                            "trans_status" => TransApiConstant::QUEUE_STAY,
                        ])->update([
                            "trans_status" => $uploadResult == true ? TransApiConstant::QUEUE_SUCCESS : TransApiConstant::QUEUE_FAIL
                        ]);
                    } catch (ValueError $ve) {
                        $imgData = $image;
                        unset($imgData["imgTransBase64"]);

                        $errMsg = [
                            "img"   => $imgData,
                            "error" => $ve->getMessage()
                        ];
                        debug_log(json_encode($errMsg, JSON_UNESCAPED_UNICODE), "genuio", GenuioConstant::IMG_TRANS);
                    }
                }

            } else if( $getGenuioObj->send_type == GenuioConstant::IMG_Ai_TRANS ){
                foreach ($images as $image) {
                    try {
                        $imgId     = (int)$image["id"];
                        $is_except = ImageConstant::IS_EXCEPT_N;
                        if( isset($image["excluded"]) && $image["excluded"] === true ) {
                            $is_except = ImageConstant::IS_EXCEPT_Y;
                        }

                        $imgObj = GenuioImageData::where([
                            "id" => $imgId
                        ])->first();
                        if( $imgObj == null ){
                            throw new ValueError(TransApiConstant::getNotHaveErrorMessage("AI_IMG_ID"));
                        }

                        $parentImgCnt = GenuioImageData::where("img_id", $imgId)->count();

                        $prdImgObj = ProductImageData::where("id", $imgId)->first();
                        if( $prdImgObj == null ){
                            throw new ValueError(TransApiConstant::getNotHaveErrorMessage("IMG_ID"));
                        }

                        if( $is_except == ImageConstant::IS_EXCEPT_Y || $prdImgObj->is_except == ImageConstant::IS_EXCEPT_Y ){
                            ProductImageData::where("id", $imgId)->update([
                                "is_except" => ImageConstant::IS_EXCEPT_Y
                            ]);
                            throw new ValueError(ImageErrorMessageConstant::getFitErrorMessage("EXCEPT_IMG"));
                        }

                        $img_url_ai_origin = $imgObj->img_url_ai;
    
                        $uploadResult   = false;
                        $errorImgFlag   = false;
                        $imgTransBase64 = "";
                        $mime           = pathinfo($img_url_ai_origin, PATHINFO_EXTENSION);
                        $dateName       = Carbon::now()->format('Y/m/d');
                        if (preg_match('/^(jpg|jpeg|png|gif)/i', $mime, $matches)) {
                            $mime = $matches[0];
                        }
                        $imgName = "/genuio/ai-img/" . $dateName . "/" . $offerId . "_" . $imgId . "_" . ($parentImgCnt+1) . "_" . $prdImgObj->img_type . "." . $mime;
                        if( isset($image["imgTransBase64"]) && !empty($image["imgTransBase64"]) ){
                            $imgTransBase64 = $image["imgTransBase64"];
                            $uploadResult   = $this->uploadAbstract->uploadFile($imgName, base64_decode($imgTransBase64));
                        } else {
                            try {
                                $fileContent = fileContents($img_url_ai_origin);
                                $status      = "";
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
                            } catch (Exception $th) {
                                $errorImgFlag   = true;
                                $imgTransBase64 = TransApiConstant::getFitErrorMessage("1688_IMG");
                            }
                        }

                        if( $uploadResult == true ) {
                            $img_url_ai = env("AWS_URL") . $imgName;
                            GenuioImageData::create([
                                "offer_id"   => $offerId,
                                "img_id"     => $imgObj->img_id,
                                "ai_type"    => GenuioConstant::IMG_Ai_TRANS,
                                "is_origin"  => GenuioConstant::IS_ORIGIN_N,
                                "img_url_ai" => $img_url_ai,
                            ]);

                            ProductImageData::where("id", $imgId)->update([
                                "img_url_trans" => $img_url_ai
                            ]);
                        } else {
                            /** 어떠한 이유로 S3로 업로드 실패 시 제외처리 */
                            ProductImageData::where("id", $imgId)->update([
                                "is_except" => ImageConstant::IS_EXCEPT_Y,
                            ]);
                        }
        
                        GenuioQueueDetailData::where([
                            "queue_id"     => $jobId,
                            "img_id"       => $imgId,
                            "trans_status" => TransApiConstant::QUEUE_STAY,
                        ])->update([
                            "trans_status" => $uploadResult == true ? TransApiConstant::QUEUE_SUCCESS : TransApiConstant::QUEUE_FAIL
                        ]);
                    } catch (ValueError $ve) {
                        $imgData = $image;
                        unset($imgData["imgTransBase64"]);

                        $errMsg = [
                            "img"   => $imgData,
                            "error" => $ve->getMessage()
                        ];
                        debug_log(json_encode($errMsg, JSON_UNESCAPED_UNICODE), "genuio", GenuioConstant::IMG_Ai_TRANS);
                    }
                }
            } else if( $getGenuioObj->send_type == GenuioConstant::IMG_TRANS_AGAIN ){
                foreach ($images as $image) {
                    try {
                        $imgId     = (int)$image["id"];
                        $is_except = ImageConstant::IS_EXCEPT_N;
                        if( isset($image["excluded"]) && $image["excluded"] === true ) {
                            $is_except = ImageConstant::IS_EXCEPT_Y;
                        }
                        
                        $imgObj = ProductImageData::where("id", $imgId)->first();
                        if( $imgObj == null ){
                            throw new ValueError(TransApiConstant::getNotHaveErrorMessage("IMG_ID"));
                        }
                        
                        if( $is_except == ImageConstant::IS_EXCEPT_Y || $imgObj->is_except == ImageConstant::IS_EXCEPT_Y ){
                            ProductImageData::where("id", $imgId)->update([
                                "is_except" => ImageConstant::IS_EXCEPT_Y
                            ]);
                            throw new ValueError(ImageErrorMessageConstant::getFitErrorMessage("EXCEPT_IMG"));
                        }
                        
                        $parentImgCnt = GenuioImageData::where("img_id", $imgId)->count();

                        $img_url_origin = $imgObj->img_url_origin;
                        $uploadResult   = false;
                        $errorImgFlag   = false;
                        $imgTransBase64 = "";
                        $img_url_trans  = "";
                        $mime           = pathinfo($img_url_origin, PATHINFO_EXTENSION);
                        $dateName       = Carbon::now()->format('Y/m/d');
                        if (preg_match('/^(jpg|jpeg|png|gif)/i', $mime, $matches)) {
                            $mime = $matches[0];
                        }
                        $imgName = "/genuio/ai-img/" . $dateName . "/" . $offerId . "_" . $imgObj->id . "_" . ($parentImgCnt+1) . "_" . $imgObj->img_type . "." . $mime;
                        if( isset($image["imgTransBase64"]) && !empty($image["imgTransBase64"]) ){
                            $imgTransBase64 = $image["imgTransBase64"];
                            $uploadResult   = $this->uploadAbstract->uploadFile($imgName, base64_decode($imgTransBase64));
                        } else {
                            try {
                                $fileContent = fileContents($img_url_origin);
                                $status      = "";
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
                            } catch (Exception $th) {
                                $errorImgFlag   = true;
                                $imgTransBase64 = TransApiConstant::getFitErrorMessage("1688_IMG");
                            }
                        }
    
                        if( $uploadResult == true ) {
                            $img_url_trans = env("AWS_URL") . $imgName;
                            ProductImageData::where("id", $imgId)->update([
                                "img_url_trans"  => $img_url_trans,
                                "trans_dated_at" => Carbon::now(),
                            ]);

                            // 1. origin 이미지
                            GenuioImageData::updateOrCreate([
                                "offer_id"  => $offerId,
                                "img_id"    => $imgId,
                                "ai_type"   => GenuioConstant::IMG_Ai_TRANS,
                                "is_origin" => GenuioConstant::IS_ORIGIN_Y,
                            ],[
                                "img_url_ai" => $img_url_origin,
                            ]);

                            // 2. 최초 번역 이미지
                            $aiImgObj = GenuioImageData::where([
                                "offer_id"   => $offerId,
                                "img_id"     => $imgId,
                                "img_url_ai" => $img_url_trans,
                                "ai_type"    => GenuioConstant::IMG_Ai_TRANS,
                                "is_origin"  => GenuioConstant::IS_ORIGIN_N
                            ])->first();
                            if( $aiImgObj == null ){
                                GenuioImageData::create([
                                    "offer_id"   => $offerId,
                                    "img_id"     => $imgId,
                                    "img_url_ai" => $img_url_trans,
                                    "ai_type"    => GenuioConstant::IMG_Ai_TRANS,
                                    "is_origin"  => GenuioConstant::IS_ORIGIN_N,
                                ]);
                            } else {
                                GenuioImageData::where("id", $imgId)->update([
                                    "updated_at" => Carbon::now()
                                ]);
                            }
                        } else {
                            /** 어떠한 이유로 S3로 업로드 실패 시 제외처리 */
                            ProductImageData::where("id", $imgId)->update([
                                "is_except"      => ImageConstant::IS_EXCEPT_Y,
                                "img_url_trans"  => $img_url_trans,
                                "trans_dated_at" => null,
                            ]);
                        }
        
                        GenuioQueueDetailData::where([
                            "queue_id"     => $jobId,
                            "img_id"       => $imgId,
                            "trans_status" => TransApiConstant::QUEUE_STAY,
                        ])->update([
                            "trans_status" => $uploadResult == true ? TransApiConstant::QUEUE_SUCCESS : TransApiConstant::QUEUE_FAIL
                        ]);
                    } catch (ValueError $ve) {
                        $imgData = $image;
                        unset($imgData["imgTransBase64"]);

                        $errMsg = [
                            "img"   => $imgData,
                            "error" => $ve->getMessage()
                        ];
                        debug_log(json_encode($errMsg, JSON_UNESCAPED_UNICODE), "genuio", GenuioConstant::IMG_TRANS_AGAIN);
                    }
                }
            }

            if( isset($params["prdObj"]["prd_desc"]) && $params["prdObj"]["prd_desc"] ){
                $prd_desc_kr = $params["prdObj"]["prd_desc"];

                GenuioAiData::updateOrCreate(
                    [
                        "offer_id" => $offerId,
                        "ai_apply" => GenuioConstant::AI_APPLY_DESC_KR
                    ],
                    [
                        "origin_data" => $prdObj->prd_desc,
                        "apply_data"  => $prd_desc_kr
                    ]
                );
            };

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        if( $getGenuioObj != null && $returnMsg["isSuccess"] === true ){
            $bindParam = [
                "offerId"       => $getGenuioObj->offer_id,
                "parent_id"     => $getGenuioObj->id,
                "send_type"     => $getGenuioObj->send_type,
                "payload_json"  => "", // base64가 너무 길어 그냥 ""처리
                "request_user"  => TransApiConstant::API_USER_COMPANY_GENUIO,
                "response_json" => json_encode($returnMsg, JSON_UNESCAPED_UNICODE),
            ];
            $queueDto = new QueueDto();
            $queueDto->bind($bindParam);
            GenuioQueueData::create($queueDto->getAllProperties());

            // 상세 이미지 업데이트
            upPrdDescTrans($offerId);

            // 변역 완료 여부 체크
            chkTransStatus($getGenuioObj->offer_id);

            /** 이지셀 W 상품 전송 */
            $sendEasysell = MallConstant::AUTO_REGIST_FALSE;
            if( isset($payloadJson["params"]["send_easysell"]) ){
                $sendEasysell = $payloadJson["params"]["send_easysell"];
            }
            if( env('APP_ENV', 'local') === "production" && $sendEasysell === MallConstant::AUTO_REGIST_TRUE ){
                $easyWCnt = EasysellProductLog::where([
                    "offer_id"       => $offerId,
                    "w_type"         => EasySellConstant::TYPE_W,
                    "regist_success" => MallConstant::REGIST_SUCCESS,
                ])->count();

                if( $easyWCnt == 0 ){
                    $options = "--func=productRegist --offerids=" . helperEscape($offerId) . " --type=" . helperEscape(EasySellConstant::TYPE_W);
                    $command = "nohup " . $this->phpAlias . " artisan easy_sell_command " . $options . " > /dev/null 2>&1 &";
                    $process = Process::fromShellCommandline($command);
                    $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
                    $process->setTimeout(null); // 실행 시간 제한 없음
                    $process->start();
                }
            }

            // 수정 상품 저장
            saveModiProduct($offerId);


            // GenuioQueueData::where([
            //     "id" => $getGenuioObj->offer_id,
            // ])->delete();
        }

        return $returnMsg;
    }

    /**
     * @func imgTransRequest
     * @description '상품 이미지 번역 요청'
     * @param array $offerIds
     */
    public function imgTransRequest(array $offerIds): array
    {
        $returnMsg = $this->returnMsg;

        try {
            foreach ($offerIds as $offerId) {
                $product1688ImageDtoList = [];
                
                $offerId = (int)$offerId;
                $imgObjs = ProductImageData::where("offer_id", $offerId)->where("lang", WConstant::WAPP_KR)->get();
                foreach ($imgObjs as $imgObj) {
                    $is_except = $imgObj->is_except;
                    if( $is_except == ImageConstant::IS_EXCEPT_Y ){
                        continue;
                    }

                    $product1688ImageDto = new Product1688ImageDto();
                    $product1688ImageDto->bind([
                        "offerId"        => $offerId,
                        "imgType"        => $imgObj->img_type,
                        "lang"           => $imgObj->lang,
                        "is_except"      => $is_except,
                        "img_url_origin" => $imgObj->img_url_origin,
                        "img_url_trans"  => "",
                        "isChangeImg"    => ImageConstant::IS_CHANGE_IMG,
                        "width"          => 800,
                        "height"         => 800,
                        "byte"           => 8,
                        "mime"           => "image/jpeg",
                    ]);
                    $product1688ImageDtoList[] = $product1688ImageDto;
                }
                $createResult = $this->createTransProductImgAgain($product1688ImageDtoList, $offerId, GenuioConstant::PRIORITY_TRUE);
                if( $createResult["isSuccess"] != true ){
                    throw new Exception($createResult["msg"]);
                }
            }

            $returnMsg = helpers_success_message(true, "번역 요청이 완료되었습니다.");
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }
        
        return $returnMsg;
    }

    /**
     * @func imgAiRegist
     * @description '상품 AI 이미지 저장'
     * @param int $offerId
     * @param array $images
     */
    public function imgAiRegist(int $offerId, array $images): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $resultImgs = [];

            foreach ($images as $image) {
                $imgId = $image["id"];
                try {
                    $dateName = Carbon::now()->format('Y/m/d');
                    $imgObj   = ProductImageData::where([
                        "id"       => $imgId,
                        "offer_id" => $offerId
                    ])->first();
                    if( $imgObj == null ){
                        throw new ValueError(ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGE"));
                    }

                    $imgId = GenuioImageData::insertGetId([
                        "offer_id"   => $offerId,
                        "img_id"     => $imgObj->id,
                        "ai_type"    => GenuioConstant::IMG_Ai_TRANS,
                        "is_origin"  => GenuioConstant::IS_ORIGIN_N,
                        "img_url_ai" => "",
                        "created_at"  => Carbon::now()
                    ]);

                    $parentImgCnt = GenuioImageData::where("img_id", $imgObj->id)->count();

                    $img_url_origin = $imgObj->img_url_origin;
                    $mime           = pathinfo($img_url_origin, PATHINFO_EXTENSION);
                    if (preg_match('/^(jpg|jpeg|png|gif)/i', $mime, $matches)) {
                        $mime = $matches[0];
                    }
                    $imgName        = "/genuio/ai-img/" . $dateName . "/" . $offerId . "_" . $imgObj->id . "_" . ($parentImgCnt+1) . "_" . $imgObj->img_type . "." . $mime;
                    $uploadResult   = $this->uploadAbstract->uploadFile($imgName, base64_decode($image["base64"]));

                    if( $uploadResult == true ) {
                        $img_url_ai = env("AWS_URL") . $imgName;
                        GenuioImageData::where("id", $imgId)->update([
                            "img_url_ai" => $img_url_ai
                        ]);

                        ProductImageData::where("id", $imgObj->id)->update([
                            "img_url_trans" => $img_url_ai
                        ]);
                    } else {
                        GenuioImageData::where("id", $imgId)->forceDelete();
                        throw new ValueError(ImageErrorMessageConstant::getFitErrorMessage("S3_IMG_UPLOAD"));
                    }

                    $resultImgs[] = [
                        "id"        => $imgId,
                        "isSuccess" => true,
                    ];
                } catch (ValueError $ve) {
                    $resultImgs[] = [
                        "id"        => $imgId,
                        "isSuccess" => false,
                        "msg"       => $ve->getMessage()
                    ];
                }
            }

            // 상세이미지 업데이트
            upPrdDescTrans($offerId);
            
            // 수정 상품 저장
            saveModiProduct($offerId);

            $returnMsg = helpers_success_message($resultImgs);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func imgAiTransRequest
     * @description '이미지 별 AI 알고리즘 요청'
     * @param array $imgIds
     * @return array
     */
    public function imgAiTransRequest(array $imgIds): array
    {
        $returnMsg = $this->returnMsg;

        try {
            foreach ($imgIds as $imgId) {
                $imgId    = (int)$imgId;
                $aiImgObj = GenuioImageData::where([
                    "id" => $imgId
                ])->first();
                $imgObj  = ProductImageData::where("id", $aiImgObj->img_id)->first();
                $offerId = $aiImgObj->offer_id;
                try {
                    $insWhere  = [
                        "offer_id"      => $offerId,
                        "parent_id"     => 0,
                        "send_type"     => GenuioConstant::IMG_Ai_TRANS,
                        "payload_json"  => "",
                        "request_user"  => TransApiConstant::API_USER_COMPANY_OC,
                        "response_json" => "",
                        "created_at"    => Carbon::now()
                    ];
                    $nextId = GenuioQueueData::insertGetId($insWhere);
        
                    $payload = [
                        "jobId"  => $nextId,
                        "images" => []
                    ];
                    
                    $isThumbnail = false;
                    if( $imgObj->img_type != ImageConstant::IMAGE_TYPE_DESC ){
                        $isThumbnail = true;
                    }
                    $payload["images"][] = [
                        "id"          => $aiImgObj->id,
                        "imagePath"   => $aiImgObj->img_url_ai,
                        "isThumbnail" => $isThumbnail,
                        "priority"    => GenuioConstant::PRIORITY_TRUE,
                        "img_type"    => $imgObj->img_type,
                        "lang"        => $imgObj->lang,
                    ];

                    $queueDetailInsList[] = [
                        "queue_id"     => $nextId,
                        "img_id"       => $imgObj->id,
                        "trans_status" => TransApiConstant::QUEUE_STAY,
                        "base64"       => "",
                        "created_at"   => Carbon::now()
                    ];
        
                    if( count($payload["images"]) > 0 ){
                        $apiResult = $this->apiCurl("post", "/translate-img", $payload);
        
                        $upWhere  = [
                            "payload_json"  => json_encode($payload, JSON_UNESCAPED_UNICODE),
                            "response_json" => json_encode($apiResult, JSON_UNESCAPED_UNICODE)
                        ];
                        GenuioQueueData::where("id", $nextId)->update($upWhere);
            
                        foreach ($queueDetailInsList as $queueDetailIns) {
                            GenuioQueueDetailData::insert($queueDetailIns);
                        }
                    }    
                    $returnMsg = helpers_success_message();
                } catch (Exception $e) {
                    $errorMsg  = "offerId: {$offerId} | errorTitle: " . TransApiConstant::getFitErrorMessage("TRANS_REQUEST_IMAGE") . "errorDesc: " . $e->getMessage();
                    $returnMsg = helpers_fail_message($errorMsg);
                }
            }

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func imgThumnailTransRequest
     * @description '상품 썸네일 이미지 번역 요청'
     * @param int $offerId
     * @return array
     */
    public function imgThumnailTransRequest(int $offerId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $imgObjs = ProductImageData::where("offer_id", $offerId)->where("lang", WConstant::WAPP_KR)->whereIn("img_type", [ImageConstant::IMAGE_TYPE_MAIN, ImageConstant::IMAGE_TYPE_SUB])->get();
            foreach ($imgObjs as $imgObj) {
                $is_except = $imgObj->is_except;
                if( $is_except == ImageConstant::IS_EXCEPT_Y ){
                    continue;
                }

                $product1688ImageDto = new Product1688ImageDto();
                $product1688ImageDto->bind([
                    "offerId"        => $offerId,
                    "imgType"        => $imgObj->img_type,
                    "lang"           => $imgObj->lang,
                    "is_except"      => $is_except,
                    "img_url_origin" => $imgObj->img_url_origin,
                    "img_url_trans"  => "",
                    "isChangeImg"    => ImageConstant::IS_CHANGE_IMG,
                    "width"          => 800,
                    "height"         => 800,
                    "byte"           => 8,
                    "mime"           => "image/jpeg",
                ]);
                $product1688ImageDtoList[] = $product1688ImageDto;
            }
            $createResult = $this->createTransProductImgAgain($product1688ImageDtoList, $offerId, GenuioConstant::PRIORITY_TRUE);
            if( $createResult["isSuccess"] != true ){
                throw new Exception($createResult["msg"]);
            }

            $returnMsg = helpers_success_message([], "번역 요청이 완료되었습니다.");
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func imgDescTransRequest
     * @description '상품 상세 이미지 번역 요청'
     * @param int $offerId
     * @return array
     */
    public function imgDescTransRequest(int $offerId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $imgObjs = ProductImageData::where("offer_id", $offerId)->where("lang", WConstant::WAPP_KR)->where("img_type", ImageConstant::IMAGE_TYPE_DESC)->get();
            foreach ($imgObjs as $imgObj) {
                $is_except = $imgObj->is_except;
                if( $is_except == ImageConstant::IS_EXCEPT_Y ){
                    continue;
                }

                $product1688ImageDto = new Product1688ImageDto();
                $product1688ImageDto->bind([
                    "offerId"        => $offerId,
                    "imgType"        => $imgObj->img_type,
                    "lang"           => $imgObj->lang,
                    "is_except"      => $is_except,
                    "img_url_origin" => $imgObj->img_url_origin,
                    "img_url_trans"  => "",
                    "isChangeImg"    => ImageConstant::IS_CHANGE_IMG,
                    "width"          => 800,
                    "height"         => 800,
                    "byte"           => 8,
                    "mime"           => "image/jpeg",
                ]);
                $product1688ImageDtoList[] = $product1688ImageDto;
            }
            $createResult = $this->createTransProductImgAgain($product1688ImageDtoList, $offerId, GenuioConstant::PRIORITY_TRUE);
            if( $createResult["isSuccess"] != true ){
                throw new Exception($createResult["msg"]);
            }

            $returnMsg = helpers_success_message([], "번역 요청이 완료되었습니다.");
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func channelImgTransRequest
     * @description '채널별 번역 큐등록'
     * @param string $channel
     * @param array $params
     * @return array
    */
    public function channelImgTransRequest(string $channel, array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $wappDomain = env("WAPP_DOMAIN", "https://task-1688.onch3.co.kr");
            switch ($channel) {
                case MallConstant::MALL_ONCHANNEL:
                default:
                    $callback_url = $wappDomain . "/api/mall/{$channel}/genuio/img/trans";
                    $qry = OcGeQueueData::class;
                    break;
            }

            $insWhere  = [
                "parent_id"     => 0,
                "send_type"     => GenuioConstant::IMG_TRANS,
                "payload_json"  => "",
                "request_user"  => MallConstant::MALL_ONCHANNEL,
                "response_json" => "",
                "created_at"    => Carbon::now()
            ];
            $nextId = $qry::insertGetId($insWhere);

            $payload = [
                "jobId"            => $nextId,
                "channel_queue_id" => $params["channel_queue_id"],
                "member_id"        => $params["member_id"],
                "channel"          => $channel,
                "callback_url"     => $callback_url,
                "images"           => [],
            ];

            foreach ($params["images"] as $data) {
                $imgId       = $data["id"];
                $offerId     = $data["offer_id"];
                $prdImgId    = $data["img_id"];
                $imgType     = $data["img_type"];
                $originUrl   = "";
                $isThumbnail = false;
                $priority    = false;
                if( isset($data["origin_url"]) ){
                    $originUrl = $data["origin_url"];
                }
                if( isset($data["isThumbnail"]) ){
                    $isThumbnail = $data["isThumbnail"];
                }
                if( isset($data["priority"]) ){
                    $priority = $data["priority"];
                }
                if( in_array($imgType, [ImageConstant::IMAGE_TYPE_MAIN, ImageConstant::IMAGE_TYPE_SUB]) ){
                    $isThumbnail = true;
                }

                if( $originUrl ){
                    $payload["images"][] = [
                        "id"          => $imgId,
                        "offer_id"    => $offerId,
                        "img_id"      => $prdImgId,
                        "origin_url"  => $originUrl,
                        "isThumbnail" => $isThumbnail,
                        "priority"    => $priority,
                    ];
                }
            }

            if( count($payload["images"]) > 0 ){
                $apiResult = $this->apiCurl("post", "/translate-img-channel", $payload);
                $upWhere = [];
                $upWhere["payload_json"] = json_encode($payload, JSON_UNESCAPED_UNICODE);

                if( $apiResult["isSuccess"] == true ){
                    $upWhere["response_json"] = json_encode($apiResult["data"], JSON_UNESCAPED_UNICODE);
                } else {
                    $upWhere["response_json"] = json_encode($apiResult, JSON_UNESCAPED_UNICODE);
                }

                $qry::where("id", $nextId)->update($upWhere);
                if( $apiResult["isSuccess"] == true ){
                    $returnMsg = helpers_success_message();
                } else {
                    $returnMsg = helpers_fail_message($apiResult["msg"]);
                }
            }

        } catch (Exception $e) {
            $errorMsg  = "errorTitle: " . TransApiConstant::getFitErrorMessage("TRANS_REQUEST_IMAGE") . "errorDesc: " . $e->getMessage();
            $returnMsg = helpers_fail_message($errorMsg);
        }

        return $returnMsg;
    }

    /**
     * @func channelImgTrans
     * @description '번역된 이미지 처리'
     * @param string $channel
     * @param array $params
     * @return array
    */
    public function channelImgTrans(string $channel, array $params): array
    {
        $returnMsg = $this->returnMsg;

        // $debugLog = [
        //     "params" => $params
        // ];
        // debug_log(json_encode($debugLog, JSON_UNESCAPED_UNICODE), "genuio/channelImgTrans", "channelImgTrans");

        try {
            $jobId  = (int)$params["jobId"];
            $images = $params["images"];

            switch ($channel) {
                case MallConstant::MALL_ONCHANNEL:
                default:
                    $qry = OcGeQueueData::class;
                    break;
            }

            $getGenuioObj = $qry::where([
                "id"           => $jobId,
                "request_user" => $channel,
                "parent_id"    => 0
            ])->first();
            if( $getGenuioObj == null ) {
                throw new Exception(TransApiConstant::getNotHaveErrorMessage("JOB_ID"));
            }

            $payloadJson      = json_decode($getGenuioObj->payload_json, JSON_UNESCAPED_UNICODE);
            $member_id        = $payloadJson["member_id"];
            $channel_queue_id = $payloadJson["channel_queue_id"];

            $resPayload = [
                "jobId"            => $jobId,
                "channel_queue_id" => $channel_queue_id,
                "member_id"        => $member_id,
                "images"           => [],
            ];
            if( $getGenuioObj->send_type == GenuioConstant::IMG_TRANS ){
                foreach ($images as $image) {
                    $img_id         = 0;
                    $img_url_origin = "";

                    try {
                        $img_id            = $image["id"];
                        $img_url_origin    = $image["origin_url"];
                        $uploadResult      = false;
                        $uploadResultClean = false;
                        $imgTransBase64    = "";
                        $fileMessage       = "";
                        $text_data         = "";
                        $mime              = pathinfo($img_url_origin, PATHINFO_EXTENSION);
                        $dateName          = Carbon::now()->format('Ymd_His');
                        if (preg_match('/^(jpg|jpeg|png|gif)/i', $mime, $matches)) {
                            $mime = $matches[0];
                        }

                        if( isset($imamge["text_data"]) ){
                            $text_data = $imamge["text_data"];
                        }

                        $imgName  = "/" . $channel . "/" . $member_id . "/product/" . $dateName . "_" . $img_id . "." . $mime;
                        if( isset($image["imgTransBase64"]) && !empty($image["imgTransBase64"]) ){
                            $imgTransBase64 = $image["imgTransBase64"];
                            $uploadResult   = $this->uploadAbstract->uploadFile($imgName, base64_decode($imgTransBase64));
                        } else {
                            try {
                                $fileContent = fileContents($img_url_origin);
                                $status      = "";
                                if( isset($image["status"]) ) {
                                    $status = $image["status"];
                                }
                                $message = "";
                                if( isset($image["message"]) ) {
                                    $message = $image["message"];
                                }
                                $fileMessage     = "status: {$status} / message: {$message}";
                                $imgEncodeBase64 = base64_encode($fileContent);
                                $uploadResult    = $this->uploadAbstract->uploadFile($imgName, base64_decode($imgEncodeBase64));
                            } catch (Exception $th) {
                                $uploadResult = false;
                                $fileMessage  = $th->getMessage();
                            }
                        }

                        $imgNameCleaned  = "/" . $channel . "/" . $member_id . "/product/" . $dateName . "_cleaned_" . $img_id . "." . $mime;
                        if( isset($image["cleaned_base64"]) && !empty($image["cleaned_base64"]) ){
                            $imgTransBase64Clean = $image["cleaned_base64"];
                            $uploadResultClean   = $this->uploadAbstract->uploadFile($imgNameCleaned, base64_decode($imgTransBase64Clean));
                        }
    
                        if( $uploadResult == true ) {
                            $img_url_trans = env("AWS_URL") . $imgName;

                            $cleaned_url = "";
                            if( $uploadResultClean == true ){
                                $cleaned_url = env("AWS_URL") . $imgNameCleaned;
                            }
                            $resPayload["images"][] = [
                                "id"             => $img_id,
                                "origin_url"     => $img_url_origin,
                                "translated_url" => $img_url_trans,
                                "cleaned_url"    => $cleaned_url,
                                "text_data"      => $text_data,
                                "status"         => OnchannelConstant::CALLBACK_SUCCESS,
                                "message"        => $fileMessage,
                            ];
                        } else {
                            $resPayload["images"][] = [
                                "id"             => $img_id,
                                "origin_url"     => $img_url_origin,
                                "translated_url" => "",
                                "cleaned_url"    => "",
                                "text_data"      => $text_data,
                                "status"         => OnchannelConstant::CALLBACK_FAIL,
                                "message"        => $fileMessage,
                            ];
                        }

                    } catch (ValueError $ve) {
                        $resPayload["images"][] = [
                            "id"             => $img_id,
                            "origin_url"     => $img_url_origin,
                            "translated_url" => "",
                            "cleaned_url"    => "",
                            "text_data"      => $text_data,
                            "status"         => OnchannelConstant::CALLBACK_FAIL,
                            "message"        => $ve->getMessage(),
                        ];
                    }
                }
            }
            $returnMsg = helpers_success_message($resPayload);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        if( $getGenuioObj != null ){
            $bindParam = [
                "parent_id"     => $getGenuioObj->id,
                "send_type"     => $getGenuioObj->send_type,
                "payload_json"  => json_encode($resPayload, JSON_UNESCAPED_UNICODE),
                "request_user"  => TransApiConstant::API_USER_COMPANY_GENUIO,
                "response_json" => json_encode($returnMsg, JSON_UNESCAPED_UNICODE),
            ];
            $qry::create($bindParam);

            // $qry::where("id", $getGenuioObj->id)->delete();
        }

        return $returnMsg;
    }

    /**
     * @func imgUpload
     * @description '이미지 S3 upload'
     * @param string $channel
     * @param array $params
     * @return array
    */
    public function imgUpload(string $channel, array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $member_id = $params["member_id"];
            $images    = $params["images"];

            $resPayload = [];
            foreach ($images as $image) {
                $img_id = 0;

                try {
                    $img_id            = $image["id"];
                    $uploadResult      = false;
                    $uploadResultClean = false;
                    $base64            = "";
                    $img_url_trans     = "";
                    $cleaned_base64    = "";
                    $cleaned_url       = "";
                    $dateName          = Carbon::now()->format('Ymd_His');

                    if( isset($image["base64"]) && !empty($image["base64"]) ){
                        $base64       = $image["base64"];
                        $mime         = getExtensionFromBase64($base64);
                        $imgName      = "/" . $channel . "/" . $member_id . "/product/" . $dateName . "_" . $img_id . "." . $mime;
                        $uploadResult = $this->uploadAbstract->uploadFile($imgName, base64_decode($base64));
                    }

                    if( isset($image["cleaned_base64"]) && !empty($image["cleaned_base64"]) ){
                        $cleaned_base64    = $image["cleaned_base64"];
                        $mime              = getExtensionFromBase64($cleaned_base64);
                        $imgNameClean      = "/" . $channel . "/" . $member_id . "/product/" . $dateName . "_cleaned_" . $img_id . "." . $mime;
                        $uploadResultClean = $this->uploadAbstract->uploadFile($imgNameClean, base64_decode($cleaned_base64));
                    }

                    if( $uploadResult === true ){
                        $img_url_trans = env("AWS_URL") . $imgName;
                    }
                    if( $uploadResultClean === true ){
                        $cleaned_url = env("AWS_URL") . $imgNameClean;
                    }

                    $resPayload["images"][] = [
                        "id"          => $img_id,
                        "trans_url"   => $img_url_trans,
                        "cleaned_url" => $cleaned_url,
                        "upload"      => $uploadResult,
                        "error"       => "",
                    ];
                } catch (ValueError $ve) {
                    $resPayload["images"][] = [
                        "id"          => $img_id,
                        "trans_url"   => "",
                        "cleaned_url" => "",
                        "upload"      => false,
                        "error"       => $ve->getMessage(),
                    ];
                }
            }
            $returnMsg = helpers_success_message($resPayload);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
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
            if(!is_array($apiResult)) throw new InvalidArgumentException("Error: {$result}");

            $returnMsg = helpers_success_message($apiResult);
        } catch (JsonException $e) {
            $returnMsg = helpers_fail_message("Error: 결과가 Json이 아닙니다.");
        } catch (InvalidArgumentException $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}