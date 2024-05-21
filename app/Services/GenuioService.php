<?php

namespace App\Services;

use App\Abstracts\TransApiAbstract;
use App\Abstracts\UploadAbstract;
use App\Constants\GenuioConstant;
use App\Constants\ImageConstant;
use App\Constants\ImageErrorMessageConstant;
use App\Constants\MallConstant;
use App\Constants\TransApiConstant;
use App\Constants\WConstant;
use App\Models\ApiUser;
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

class GenuioService extends TransApiAbstract
{
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
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func createTransProductImg
     * @description '이미지 번역 통신'
     * @param array $product1688ImageDtoList
     * @param int $offerId
     */
    public function createTransProductImg(array $product1688ImageDtoList, int $offerId, bool $priority = GenuioConstant::PRIORITY_FALSE): array
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
                "prdObj" => $prdObj->toArray()
            ];

            $queueDetailInsList = [];
            foreach ($product1688ImageDtoList as $product1688ImageDto) {
                if( $product1688ImageDto->is_change_img == true && $product1688ImageDto->lang == WConstant::WAPP_KR ){
                    $imgObj = ProductImageData::where([
                        "offer_id"       => $product1688ImageDto->offer_id,
                        "img_type"       => $product1688ImageDto->img_type,
                        "lang"           => $product1688ImageDto->lang,
                        "img_url_origin" => $product1688ImageDto->img_url_origin,
                    ])->first();
                    
                    $isThumbnail = false;
                    if( $imgObj->img_type != ImageConstant::IMAGE_TYPE_DESC ){
                        $isThumbnail = true;
                    }
                    $payload["images"][] = [
                        "id"          => $imgObj->id,
                        "imagePath"   => $product1688ImageDto->img_url_origin,
                        "isThumbnail" => $isThumbnail,
                        "priority"    => $priority
                    ];

                    $queueDetailInsList[] = [
                        "queue_id"     => $nextId,
                        "img_id"       => $imgObj->id,
                        "trans_status" => TransApiConstant::QUEUE_STAY,
                        "base64"       => "",
                        "created_at"   => Carbon::now()
                    ];
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
     * @description '추가 이미지 번역 통신'
     * @param array $product1688ImageDtoList
     * @param int $offerId
     */
    public function createTransProductImgAgain(array $product1688ImageDtoList, int $offerId, bool $priority = GenuioConstant::PRIORITY_FALSE): array
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
                "prdObj" => $prdObj->toArray()
            ];

            $queueDetailInsList = [];
            foreach ($product1688ImageDtoList as $product1688ImageDto) {
                if( $product1688ImageDto->is_change_img == true ){
                    $imgObj = ProductImageData::where([
                        "offer_id"       => $product1688ImageDto->offer_id,
                        "img_type"       => $product1688ImageDto->img_type,
                        "lang"           => $product1688ImageDto->lang,
                        "img_url_origin" => $product1688ImageDto->img_url_origin,
                    ])->first();
                    
                    $isThumbnail = false;
                    if( $imgObj->img_type != ImageConstant::IMAGE_TYPE_DESC ){
                        $isThumbnail = true;
                    }
                    $payload["images"][] = [
                        "id"          => $imgObj->id,
                        "imagePath"   => $product1688ImageDto->img_url_origin,
                        "isThumbnail" => $isThumbnail,
                        "priority"    => $priority
                    ];

                    $queueDetailInsList[] = [
                        "queue_id"     => $nextId,
                        "img_id"       => $imgObj->id,
                        "trans_status" => TransApiConstant::QUEUE_STAY,
                        "base64"       => "",
                        "created_at"   => Carbon::now()
                    ];
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
                "request_user" => TransApiConstant::API_USER_COMPANY_OC
            ])->first();
            if( $getGenuioObj == null ) {
                throw new Exception(TransApiConstant::getNotHaveErrorMessage("QUEUE_ID"));
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
                            ProductImageData::where("id", $imgId)->update([
                                "img_url_trans"  => $img_url_trans,
                                "trans_dated_at" => null,
                            ]);
                        }
    
                        if( $errorImgFlag == true ) {
                            // 1688 측 이미지 자체가 유효하지 않은 상태 기록
                            // $errMsg = "원본 이미지 upload error | img: " . $img_url_origin;
                            // debug_log(json_encode($errMsg, JSON_UNESCAPED_UNICODE), "genuio", "genuio-img");
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
                            ProductImageData::where("id", $imgId)->update([
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
                        "offer_id"      => $offerId,
                        "ai_apply" => GenuioConstant::AI_APPLY_DESC_KR
                    ],
                    [
                        "origin_data" => $prdObj->prd_desc,
                        "apply_data"  => $prd_desc_kr
                    ]
                );
            };

            // 상세 이미지 업데이트
            upPrdDescTrans($offerId);

            // 수정 상품 저장
            saveModiProduct($offerId);

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        if( $getGenuioObj != null ){
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

            if( $getGenuioObj->send_type == GenuioConstant::IMG_TRANS ){
                // 변역 완료 여부 체크
                chkTransStatus($getGenuioObj->offer_id);
            }
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

        if( env("APP_ENV", "local") != "production" ){
            return helpers_fail_message("운영 환경에서만 사용 가능합니다.");
        }

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

        if( env("APP_ENV", "local") != "production" ){
            return helpers_fail_message("운영 환경에서만 사용 가능합니다.");
        }

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
                        "priority"    => GenuioConstant::PRIORITY_TRUE
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

        if( env("APP_ENV", "local") != "production" ){
            return helpers_fail_message("운영 환경에서만 사용 가능합니다.");
        }

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

        if( env("APP_ENV", "local") != "production" ){
            return helpers_fail_message("운영 환경에서만 사용 가능합니다.");
        }

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
     * @func removeQueue
     * @description '큐 삭제'
     * @param int $queueId
     * @return array
     */
    public function removeQueue(int $queueId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $geObj = GenuioQueueData::where([
                "id"           => $queueId,
                "request_user" => TransApiConstant::API_USER_COMPANY_OC
            ])->first();

            if( $geObj != null ){
                $resJson   = $geObj->response_json;
                $resDecode = json_decode($resJson, JSON_UNESCAPED_UNICODE);

                $internalJobId = $resDecode["internalJobId"];

                if( $internalJobId ){
                    $endPoint = "/translate-progress/remove/{$internalJobId}?queue=priority";
                    $result = $this->apiCurl("POST", $endPoint);
                    if( $result["status"] == GenuioConstant::REMOVE_QUEUE_OK ){
                        GenuioQueueData::where("id", $geObj->id)->delete();
                    } else {
                        debug_log(json_encode($result, JSON_UNESCAPED_UNICODE), "genuio", "removeQueue");
                    }
                }
            }

            $returnMsg = helpers_success_message();
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
    public function channelImgTransRequest(string $channel = MallConstant::MALL_ONCHANNEL, array $params): array
    {
        $returnMsg = $this->returnMsg;

        if( env("APP_ENV", "local") != "production" ){
            return helpers_fail_message("운영 환경에서만 사용 가능합니다.");
        }

        try {
            $wappDomain = env("WAPP_DOMAIN", "https://task-1688.onch3.co.kr");
            switch ($channel) {
                case MallConstant::MALL_ONCHANNEL:
                default:
                    $callback_url = $wappDomain . "/mall/{$channel}/genuio/img/trans";
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
                if( $originUrl ){
                    $payload["images"][] = [
                        "imagePath"   => $originUrl,
                        "isThumbnail" => $isThumbnail,
                        "priority"    => $priority
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
    public function channelImgTrans(string $channel = MallConstant::MALL_ONCHANNEL, array $params): array
    {
        $returnMsg = $this->returnMsg;
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
                "request_user" => $channel
            ])->first();
            if( $getGenuioObj == null ) {
                throw new Exception(TransApiConstant::getNotHaveErrorMessage("QUEUE_ID"));
            }

            $payloadJson      = json_decode($getGenuioObj->payload_json, JSON_UNESCAPED_UNICODE);
            $member_id        = $payloadJson["member_id"];
            $channel_queue_id = $payloadJson["channel_queue_id"];

            $resPayload = [
                "jobId"            => $jobId,
                "channel_queue_id" => $channel_queue_id,
                "images"           => []
            ];
            if( $getGenuioObj->send_type == GenuioConstant::IMG_TRANS ){
                foreach ($images as $key => $image) {
                    try {
                        $img_url_origin = $image["origin_url"];
                        $uploadResult   = false;
                        $base64         = "";
                        $img_url_trans  = "";
                        $mime           = pathinfo($img_url_origin, PATHINFO_EXTENSION);
                        if (preg_match('/^(jpg|jpeg|png|gif)/i', $mime, $matches)) {
                            $mime = $matches[0];
                        }

                        $imgName  = $member_id . "/product/" . $jobId . "_" . $key . "." . $mime;
                        if( isset($image["base64"]) && !empty($image["base64"]) ){
                            $base64       = $image["base64"];
                            $uploadResult = $this->uploadAbstract->uploadFile($imgName, base64_decode($base64));
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
                            }
                        }
    
                        if( $uploadResult == true ) {
                            $img_url_trans = env("AWS_URL") . $imgName;
                            $resPayload["images"][] = [
                                "origin_url" => $img_url_origin,
                                "trans_url" => $img_url_trans,
                            ];
                        } else {
                            $img_url_trans = $img_url_origin;
                            $resPayload["images"][] = [
                                "origin_url"   => $img_url_origin,
                                "trans_url"    => $img_url_trans,
                                "file_message" => $fileMessage,
                            ];
                        }

                    } catch (ValueError $ve) {
                        $resPayload["images"][] = [
                            "origin_url" => $image["origin_url"],
                            "error"      => $ve->getMessage(),
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
                "payload_json"  => "", // base64가 너무 길어 그냥 ""처리
                "request_user"  => TransApiConstant::API_USER_COMPANY_GENUIO,
                "response_json" => json_encode($returnMsg, JSON_UNESCAPED_UNICODE),
            ];
            $qry::create($bindParam);
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