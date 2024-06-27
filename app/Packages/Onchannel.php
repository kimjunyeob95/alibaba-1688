<?php

namespace App\Packages;

use App\Abstracts\MallApiAbstract;
use App\Abstracts\OrderAbstract;
use App\Abstracts\ProductAbstract;
use App\Abstracts\TransApiAbstract;
use App\Constants\CategoryConstant;
use App\Constants\ImageConstant;
use App\Constants\MallConstant;
use App\Constants\MallErrorMessageConstant;
use App\Constants\OnchannelConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Models\CategoryMapping;
use App\Models\OnchannelProductDetailLog;
use App\Models\OnchannelProductLog;
use App\Models\OnchCategoryExcelDataCopy2;
use App\Models\ProductData;
use App\Models\ProductWeightData;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\Paginator;

class Onchannel extends MallApiAbstract
{
    private string $token;
    private string $domain;

    public function __construct(
        JwtPackage $jwtPackage,
        string $channel,
        OrderAbstract $orderW1,
        TransApiAbstract $transApiAbstract,
        ProductAbstract $productW1
    )
    {
        parent::__construct($jwtPackage, $channel, $orderW1, $transApiAbstract, $productW1);
        $this->token  = env("ON_TOKEN");
        $this->domain = env("OC_DOMAIN", "https://task.onch3.co.kr");
    }

    /****************************************** 상품 start **********************************************/
    
    /**
     * @func productRegist
     * @description '상품등록'
     * @param array $offerIds
     * @param array $params
     * @return array
    */
    public function productRegist(array $offerIds, array $params = []): array
    {
        $successIds   = [];
        $failIds      = [];
        $sendTypeList = [ OnchannelConstant::PRD_CHANNEL ];
        $weights      = CategoryConstant::WEIGHTS;
        if( isset($params["sendTypeList"]) ){
            $sendTypeList = $params["sendTypeList"];
        }
        foreach ($sendTypeList as $sendType) {
            foreach ($offerIds as $offerId) {
                
                upWeightStatus($offerId);

                $regCnt = OnchannelProductLog::where([
                    "offer_id"       => $offerId,
                    "member_id"      => OnchannelConstant::ONCH1688,
                    "send_type"      => $sendType,
                    "regist_success" => MallConstant::REGIST_SUCCESS,
                ])->count();

                if( $regCnt == 0 ){
                    try {
                        $prdObj = ProductData::with([
                            "main_img",
                            "no_except_sub_imgs",
                            "extends",
                            "images",
                            "no_except_options",
                            "no_except_notices",
                            "w_mapping",
                            "oc_mapping",
                        ])->where("offer_id", $offerId)->first();

                        if( $prdObj == null ){
                            throw new Exception(MallErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));
                        }
    
                        if(count($prdObj->no_except_options) < 1){
                            throw new Exception(MallErrorMessageConstant::getNotHaveErrorMessage("OPTIONS"));
                        }
    
                        if( $prdObj->w_mapping == null ){
                            throw new Exception(MallErrorMessageConstant::getFitErrorMessage("W_APP_MAPPINGCODE"));
                        }
    
                        if( $prdObj->oc_mapping == null ){
                            throw new Exception(MallErrorMessageConstant::getFitErrorMessage("OC_MAPPINGCODE"));
                        }

                        if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                            if( $prdObj->trans_stauts == ProductConstant::TRANS_STATUS_N ){
                                throw new Exception(MallErrorMessageConstant::getFitErrorMessage("TRANS_STATUS"));
                            }
                            if( $prdObj->prd_desc_kr == "" ){
                                throw new Exception(MallErrorMessageConstant::getFitErrorMessage("PRD_DESC_KR"));
                            }
                        }
    
                        $prd_desc     = $prdObj->prd_desc;
                        if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                            $prd_desc = $prdObj->prd_desc_kr;
                        }

                        $noticeInfo   = $prdObj->no_except_notices->pluck("attribute_value_kr","attribute_name_kr")->toArray();
                        $notice_desc  = getNoticeInfoTable($noticeInfo);
                        $prd_desc    .= $notice_desc;

                        $prd_desc .= "<div style='text-align: center !important'>" . $prd_desc . "</div>";
            
                        $images = [];
                        foreach ($prdObj->images as $imgObj) {
                            if( $imgObj->is_except == ImageConstant::IS_EXCEPT_N && $imgObj->lang == WConstant::WAPP_KR ){
                                if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                                    if( $imgObj->img_url_trans == "" ){
                                        $msg = $imgObj->img_url_origin . " 번역 미완료 이미지";
                                        throw new Exception($msg);
                                    }

                                    $images[] = [
                                        "img_type" => $imgObj->img_type,
                                        "img_url"  => $imgObj->img_url_trans
                                    ];
                                } else {
                                    $images[] = [
                                        "img_type" => $imgObj->img_type,
                                        "img_url"  => $imgObj->img_url_origin
                                    ];
                                }
                            }
                        }

                        $img_url = $prdObj->main_img->img_url_origin;
                        if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                            $img_url = $prdObj->main_img->img_url_trans;
                        }

                        $img_nm_550 = "";
                        $img_nm_300 = "";
                        $img_nm_130 = "";
                        if( isset($prdObj->no_except_sub_imgs[0]->img_url_origin) ){
                            $img_nm_550 = $prdObj->no_except_sub_imgs[0]->img_url_origin;
                            if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                                $img_nm_550 = $prdObj->no_except_sub_imgs[0]->img_url_trans;
                            }
                        }
                        if( isset($prdObj->no_except_sub_imgs[1]->img_url_origin) ){
                            $img_nm_300 = $prdObj->no_except_sub_imgs[1]->img_url_origin;
                            if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                                $img_nm_300 = $prdObj->no_except_sub_imgs[1]->img_url_trans;
                            }
                        }
                        if( isset($prdObj->no_except_sub_imgs[2]->img_url_origin) ){
                            $img_nm_130 = $prdObj->no_except_sub_imgs[2]->img_url_origin;
                            if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                                $img_nm_130 = $prdObj->no_except_sub_imgs[2]->img_url_trans;
                            }
                        }

                        $delivery_price = ProductConstant::WEIGHT_STATUS_NONE_PRICE;
                        $weightObj      = ProductWeightData::where("offer_id", $prdObj->offer_id)->first();

                        if( $weightObj != null ){
                            $delivery_price = $weights[$weightObj->weight];
                        }

                        $payload = [
                            "nat_sec"         => OnchannelConstant::NAT_SEC,
                            "supp_sec"        => OnchannelConstant::SUPP_SEC,
                            "jejo_code"       => $prdObj->offer_id,
                            "product_nm"      => $prdObj->prd_name_kr,
                            "prd_char1"       => $prdObj->minor_not_sale,
                            "trans_info"      => OnchannelConstant::TRANS_INFO,
                            "send_check"      => OnchannelConstant::SEND_CHECK,
                            "send_price"      => (int)0,
                            "jeju_send_price" => (int)$prdObj->extends->send_jeju_price,
                            "etc_send_price"  => (int)$prdObj->extends->send_etc_price,
                            "trans_nm"        => OnchannelConstant::TRANS_NM,
                            "prd_channel"     => $sendType,
                            "sale_num"        => OnchannelConstant::SALE_NUM,
                            "etc_comment"     => OnchannelConstant::ETC_COMMENT,
                            "return_comment"  => $prdObj->return_comment,
                            "sec_tax"         => OnchannelConstant::SEC_TAX,
                            "subject"         => $prdObj->prd_name_kr,
                            "contents"        => $prd_desc,
                            "img_url"         => $img_url,
                            "img_nm_550"      => $img_nm_550,
                            "img_nm_300"      => $img_nm_300,
                            "img_nm_130"      => $img_nm_130,
                            "min_count"       => $prdObj->start_quantity,
                            "images"          => $images,
                            "cate_num"        => 26,
                            "store_code"      => (string)$prdObj->oc_mapping->mapping_code,
                            "brand_info"      => OnchannelConstant::BRAND_INFO
                        ];
            
                        $options = [];
                        foreach ($prdObj->no_except_options as $option) {
                            $ocPrice = ocPrice($option->price_1688_option, (int)$delivery_price);
                            // $ocPrice = ocPrice($option->price_1688, (int)$delivery_price);

                            $options[] = [
                                "op_rank"      => "1",
                                "op_code"      => $option->id,
                                "option_nm"    => $option->option_name_kr,
                                "cus_price"    => $ocPrice["cus_price"],
                                "disc_price"   => 0,
                                "option_price" => 0,
                                "vendor_price" => 0,
                                "onch_price"   => $ocPrice["onch_price"],
                                "total_count"  => 0,
                                "weight"       => $option->weight,
                                "volume"       => "",
                                "amount"       => 0
                            ];
                        }
                        $payload["options"] = $options;
                        $header = array(
                            'Content-type: application/json',
                            'Authorization: Bearer ' . $this->token,
                        );
                        $endPoint   = $this->domain . "/api/v1/product/regist";
                        $resultCurl = helpers_curl("POST", $endPoint, $header, $payload);
    
                        if( isset($resultCurl["prd_code"]) && $resultCurl["prd_code"] ){
                            $log = OnchannelProductLog::updateOrCreate(
                                [
                                    "offer_id"  => $offerId,
                                    "member_id" => OnchannelConstant::ONCH1688,
                                    "send_type" => $sendType,
                                ],
                                [
                                    "prd_code"       => $resultCurl["prd_code"],
                                    "regist_success" => MallConstant::REGIST_SUCCESS,
                                    "message"        => "",
                                    "registed_at"    => Carbon::now(),
                                ]
                            );

                            OnchannelProductDetailLog::create([
                                "log_id"     => $log->id,
                                "send_type"  => MallConstant::SEND_TYPE_REGIST,
                                "is_success" => MallConstant::REGIST_SUCCESS,
                                "message"    => ""
                            ]);

                            $successIds[] = $offerId;
                        } else {
                            $msg = "온채널 통신 에러";
                            if( isset($resultCurl["msg"]) ){
                                $msg = $resultCurl["msg"];
                            } else {
                                if(is_array($resultCurl)){
                                    $resultCurl = json_encode($resultCurl, JSON_UNESCAPED_UNICODE);
                                }
                                debug_log($resultCurl, "onchannel/prdRegist", "prdRegist");
                            }
                            $log = OnchannelProductLog::updateOrCreate(
                                [
                                    "offer_id"  => $offerId,
                                    "member_id" => OnchannelConstant::ONCH1688,
                                    "send_type" => $sendType,
                                ],
                                [
                                    "prd_code"       => 0,
                                    "regist_success" => MallConstant::REGIST_ERROR,
                                    "message"        => $msg,
                                ]
                            );

                            OnchannelProductDetailLog::create([
                                "log_id"     => $log->id,
                                "send_type"  => MallConstant::SEND_TYPE_REGIST,
                                "is_success" => MallConstant::REGIST_FAIL,
                                "message"    => $msg
                            ]);
                        }
                    } catch (Exception $e) {
                        $failIds[] = [
                            "offer_id" => $offerId,
                            "msg"      => $e->getMessage()
                        ];
    
                        $log = OnchannelProductLog::updateOrCreate(
                            [
                                "offer_id"  => $offerId,
                                "member_id" => OnchannelConstant::ONCH1688,
                                "send_type" => $sendType,
                            ],
                            [
                                "prd_code"       => 0,
                                "regist_success" => MallConstant::REGIST_ERROR,
                                "message"        => $e->getMessage(),
                            ]
                        );

                        OnchannelProductDetailLog::create([
                            "log_id"     => $log->id,
                            "send_type"  => MallConstant::SEND_TYPE_REGIST,
                            "is_success" => MallConstant::REGIST_FAIL,
                            "message"    => $e->getMessage(),
                        ]);
                    }
                } else {
                    $modiResult = $this->productModi([$offerId]);
                    if( $modiResult["isSuccess"] === true ){
                        $successIds[] = $offerId;
                    } else {
                        $failIds[] = [
                            "offer_id" => $offerId,
                            "msg"      => $modiResult["msg"]
                        ];
                    }
                }

                sleep(1);
            }
        }

        $result = ["success" => $successIds, "fail" => $failIds];

        return helpers_success_message($result);
    }

    /**
     * @func productModi
     * @description '상품수정'
     * @param array $offerIds
     * @param int $sendType
     * @return array
    */
    public function productModi(array $offerIds, int $sendType = OnchannelConstant::PRD_CHANNEL): array
    {
        $returnMsg = $this->returnMsg;
        $weights   = CategoryConstant::WEIGHTS;

        foreach ($offerIds as $offerId) {
            $logObj = OnchannelProductLog::where([
                "offer_id"       => $offerId,
                "member_id"      => OnchannelConstant::ONCH1688,
                "send_type"      => $sendType,
                "regist_success" => MallConstant::REGIST_SUCCESS,
            ])->first();

            if( $logObj != null ){
                try {
                    $prdObj = ProductData::with([
                        "main_img",
                        "no_except_sub_imgs",
                        "extends",
                        "images",
                        "no_except_options",
                        "no_except_notices",
                        "w_mapping",
                        "oc_mapping",
                        "oc_public_log",
                    ])->where("offer_id", $offerId)->first();

                    if( $prdObj == null ){
                        throw new Exception(MallErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));
                    }

                    if(count($prdObj->no_except_options) < 1){
                        throw new Exception(MallErrorMessageConstant::getNotHaveErrorMessage("OPTIONS"));
                    }

                    if( $prdObj->w_mapping == null ){
                        throw new Exception(MallErrorMessageConstant::getFitErrorMessage("W_APP_MAPPINGCODE"));
                    }

                    if( $prdObj->oc_mapping == null ){
                        throw new Exception(MallErrorMessageConstant::getFitErrorMessage("OC_MAPPINGCODE"));
                    }

                    if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                        if( $prdObj->trans_stauts == ProductConstant::TRANS_STATUS_N ){
                            throw new Exception(MallErrorMessageConstant::getFitErrorMessage("TRANS_STATUS"));
                        }
                        if( $prdObj->prd_desc_kr == "" ){
                            throw new Exception(MallErrorMessageConstant::getFitErrorMessage("PRD_DESC_KR"));
                        }
                    }

                    $prd_desc     = $prdObj->prd_desc;
                    if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                        $prd_desc = $prdObj->prd_desc_kr;
                    }

                    $noticeInfo   = $prdObj->no_except_notices->pluck("attribute_value_kr","attribute_name_kr")->toArray();
                    $notice_desc  = getNoticeInfoTable($noticeInfo);
                    $prd_desc    .= $notice_desc;

                    $prd_desc .= "<div style='text-align: center !important'>" . $prd_desc . "</div>";
        
                    $images = [];
                    foreach ($prdObj->images as $imgObj) {
                        if( $imgObj->is_except == ImageConstant::IS_EXCEPT_N && $imgObj->lang == WConstant::WAPP_KR ){
                            if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                                if( $imgObj->img_url_trans == "" ){
                                    $msg = $imgObj->img_url_origin . " 번역 미완료 이미지";
                                    throw new Exception($msg);
                                }

                                $images[] = [
                                    "img_type" => $imgObj->img_type,
                                    "img_url"  => $imgObj->img_url_trans
                                ];
                            } else {
                                $images[] = [
                                    "img_type" => $imgObj->img_type,
                                    "img_url"  => $imgObj->img_url_origin
                                ];
                            }
                        }
                    }

                    $img_url = $prdObj->main_img->img_url_origin;
                    if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                        $img_url = $prdObj->main_img->img_url_trans;
                    }

                    $img_nm_550 = "";
                    $img_nm_300 = "";
                    $img_nm_130 = "";
                    if( isset($prdObj->no_except_sub_imgs[0]->img_url_origin) ){
                        $img_nm_550 = $prdObj->no_except_sub_imgs[0]->img_url_origin;
                        if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                            $img_nm_550 = $prdObj->no_except_sub_imgs[0]->img_url_trans;
                        }
                    }
                    if( isset($prdObj->no_except_sub_imgs[1]->img_url_origin) ){
                        $img_nm_300 = $prdObj->no_except_sub_imgs[1]->img_url_origin;
                        if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                            $img_nm_300 = $prdObj->no_except_sub_imgs[1]->img_url_trans;
                        }
                    }
                    if( isset($prdObj->no_except_sub_imgs[2]->img_url_origin) ){
                        $img_nm_130 = $prdObj->no_except_sub_imgs[2]->img_url_origin;
                        if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                            $img_nm_130 = $prdObj->no_except_sub_imgs[2]->img_url_trans;
                        }
                    }

                    $delivery_price = ProductConstant::WEIGHT_STATUS_NONE_PRICE;
                    $weightObj      = ProductWeightData::where("offer_id", $prdObj->offer_id)->first();

                    if( $weightObj != null ){
                        $delivery_price = $weights[$weightObj->weight];
                    }

                    $payload = [
                        "prd_code"        => $prdObj->oc_public_log->prd_code,
                        "nat_sec"         => OnchannelConstant::NAT_SEC,
                        "supp_sec"        => OnchannelConstant::SUPP_SEC,
                        "jejo_code"       => $prdObj->offer_id,
                        "product_nm"      => $prdObj->prd_name_kr,
                        "prd_char1"       => $prdObj->minor_not_sale,
                        "trans_info"      => OnchannelConstant::TRANS_INFO,
                        "send_check"      => OnchannelConstant::SEND_CHECK,
                        "send_price"      => (int)0,
                        "jeju_send_price" => (int)$prdObj->extends->send_jeju_price,
                        "etc_send_price"  => (int)$prdObj->extends->send_etc_price,
                        "trans_nm"        => OnchannelConstant::TRANS_NM,
                        "prd_channel"     => $sendType,
                        "sale_num"        => OnchannelConstant::SALE_NUM,
                        "etc_comment"     => OnchannelConstant::ETC_COMMENT,
                        "return_comment"  => $prdObj->return_comment,
                        "sec_tax"         => OnchannelConstant::SEC_TAX,
                        "subject"         => $prdObj->prd_name_kr,
                        "contents"        => $prd_desc,
                        "img_url"         => $img_url,
                        "img_nm_550"      => $img_nm_550,
                        "img_nm_300"      => $img_nm_300,
                        "img_nm_130"      => $img_nm_130,
                        "min_count"       => $prdObj->start_quantity,
                        "images"          => $images,
                        "cate_num"        => 26,
                        "store_code"      => (string)$prdObj->oc_mapping->mapping_code,
                        "brand_info"      => OnchannelConstant::BRAND_INFO
                    ];
        
                    $options = [];
                    foreach ($prdObj->no_except_options as $option) {
                        $ocPrice = ocPrice($option->price_1688_option, (int)$delivery_price);
                        // $ocPrice = ocPrice($option->price_1688, (int)$delivery_price);

                        $options[] = [
                            "op_rank"      => "1",
                            "op_code"      => $option->id,
                            "option_nm"    => $option->option_name_kr,
                            "cus_price"    => $ocPrice["cus_price"],
                            "disc_price"   => 0,
                            "option_price" => 0,
                            "vendor_price" => 0,
                            "onch_price"   => $ocPrice["onch_price"],
                            "total_count"  => 0,
                            "weight"       => $option->weight,
                            "volume"       => "",
                            "amount"       => 0
                        ];
                    }
                    $payload["options"] = $options;
                    $header = array(
                        'Content-type: application/json',
                        'Authorization: Bearer ' . $this->token,
                    );

                    $endPoint = $this->domain . "/api/w/product/edit";
                    $result   = helpers_curl("POST", $endPoint, $header, $payload);
                    if( isset($result["prd_code"]) && $result["prd_code"] ){

                        OnchannelProductDetailLog::create([
                            "log_id"     => $logObj->id,
                            "send_type"  => MallConstant::SEND_TYPE_MODI,
                            "is_success" => MallConstant::REGIST_SUCCESS,
                            "message"    => ""
                        ]);

                        $returnMsg = helpers_success_message();
                    } else {
                        $msg = "온채널 통신 에러";
                        if( isset($result["msg"]) ){
                            $msg = $result["msg"];
                        } else {
                            debug_log(json_encode($result, JSON_UNESCAPED_UNICODE), "onchannel/prdModi", "prdModi");
                        }

                        OnchannelProductDetailLog::create([
                            "log_id"     => $logObj->id,
                            "send_type"  => MallConstant::SEND_TYPE_MODI,
                            "is_success" => MallConstant::REGIST_ERROR,
                            "message"    => $msg
                        ]);

                        $returnMsg = helpers_fail_message($msg);
                    }
                } catch (Exception $e) {
                    $msg = $e->getMessage();
                    OnchannelProductDetailLog::create([
                        "log_id"     => $logObj->id,
                        "send_type"  => MallConstant::SEND_TYPE_MODI,
                        "is_success" => MallConstant::REGIST_ERROR,
                        "message"    => $msg
                    ]);

                    $returnMsg = helpers_fail_message($msg);
                }
            }
        }

        return $returnMsg;
    }

    /**
     * @func sendModiProduct
     * @description '수정 된 상품 전송'
     * @return void
     */
    public function sendModiProduct(): void
    {
        debug_log("온채널 일괄 수정 전송 시작", "onchannel/sendAllPrdModi", "sendAllPrdModi");

        $builder = OnchannelProductLog::where([
            "member_id"      => OnchannelConstant::ONCH1688,
            "regist_success" => MallConstant::REGIST_SUCCESS,
        ]);

        $perPage    = 900;
        $totalCount = $builder->count();
        $totalPages = ceil($totalCount / $perPage);

        for ($page = 1; $page <= $totalPages; $page++) {
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        
            // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
            $pagedData = $builder->paginate($perPage);
            $results   = $pagedData->items();

            foreach ($results as $obj) {
                $offerId   = $obj->offer_id;
                $send_type = $obj->send_type;

                $this->productModi([$offerId], $send_type);

                sleep(1);
            }

            debug_log("온채널 일괄 수정 전송 진행중({$page}/{$totalPages})", "onchannel/sendAllPrdModi", "sendAllPrdModi");
        }
        
        debug_log("온채널 일괄 수정 전송 종료", "onchannel/sendAllPrdModi", "sendAllPrdModi");
    }

    /****************************************** 상품 end **********************************************/

    /****************************************** 카테고리 start **********************************************/

    /**
     * @func categoryMapping
     * @description '카테고리 매핑 저장'
     *
     * @return array
     */
    public function categoryMapping(): array
    {
        $returnMsg = $this->returnMsg;
        try {
            DB::beginTransaction();

            $filePath = public_path('app/oc_categories.txt');
            if (File::exists($filePath)) {
                $lines = File::lines($filePath);
                foreach($lines as $line){
                    $data = explode(',', $line);
                    $categoryObj = CategoryMapping::where("mapping_channel", ProductConstant::MAPPING_WAPP)
                        ->where("mapping_code",$data[0])
                        ->get();
                    foreach($categoryObj as $cate){
                        // 카테고리 매핑
                        CategoryMapping::updateOrCreate([
                                "category_id"     => $cate->category_id,
                                "mapping_channel" => MallConstant::MALL_ONCHANNEL
                            ],[
                                "mapping_code" => $data[1]
                            ]);
                    }
                }
            } else {
                throw new Exception("파일이 존재하지 않습니다.");
            }
            $returnMsg = helpers_success_message();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func channelCateDepth
     * @description '채널 카테고리 단계 조회'
     * @param array $params
     * @return array
    */
    public function channelCateDepth(array $params): array
    {
        $returnMsg = $this->returnMsg;
        
        try {
            $level     = $params["level"];
            $cate_name = $params["cate_name"];

            $cate_first  = "";
            $cate_second = "";
            $cate_third  = "";
            $cate_arr    = explode(",", $cate_name);
            $where = [];
            $group = [];
            if( $level == 1 ){
                $cate_first = $cate_arr[0];
                $where = [
                    "fir_cate" => $cate_first
                ];
                $group = ["se_cate"];
            } else if( $level == 2){
                $cate_first  = $cate_arr[0];
                $cate_second = $cate_arr[1];
                $where = [
                    "fir_cate"  => $cate_first,
                    "se_cate" => $cate_second,
                ];
                $group = ["th_cate"];
            } else if( $level == 3){
                $cate_first  = $cate_arr[0];
                $cate_second = $cate_arr[1];
                $cate_third  = $cate_arr[2];
                $where = [
                    "fir_cate"  => $cate_first,
                    "se_cate" => $cate_second,
                    "th_cate"  => $cate_third,
                ];
                $group = ["last_cate"];
            }

            $nextCateObjs = OnchCategoryExcelDataCopy2::where($where)
            ->orderBy("fir_cate", "asc")
            ->orderBy("se_cate", "asc")
            ->orderBy("th_cate", "asc")
            ->orderBy("last_cate", "asc")
            ->groupBy($group)->get();

            $data = [];
            foreach ($nextCateObjs as $nextCateObj) {
                $data[] = [
                    "cate_first"  => $nextCateObj->fir_cate,
                    "cate_second" => $nextCateObj->se_cate,
                    "cate_third"  => $nextCateObj->th_cate,
                    "cate_fourth" => $nextCateObj->last_cate,
                ];
            }
            $returnMsg = helpers_success_message($data);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }
        return $returnMsg;
    }

    /**
     * @func channelCateList
     * @description '채널 카테고리 목록 조회'
     * @param array $params
     * @return array
    */
    public function channelCateList(array $params): array
    {
        $returnMsg = $this->returnMsg;
        
        try {
            $keyword     = $params["keyword"];
            $cate_first  = $params["cate_first"];
            $cate_second = $params["cate_second"];
            $cate_third  = $params["cate_third"];
            $cate_fourth = $params["cate_fourth"];
            
            $builder = OnchCategoryExcelDataCopy2::query();

            if( $cate_first != "" ){
                $builder->where("fir_cate", $cate_first);
            }
            if( $cate_second != "" ){
                $builder->where("se_cate", $cate_second);
            }
            if( $cate_third != "" ){
                $builder->where("th_cate", $cate_third);
            }
            if( $cate_fourth != "" ){
                $builder->where("last_cate", $cate_fourth);
            }
            if( $keyword != "" ){
                $builder->where(function($query) use ($keyword) {
                    $query->where("fir_cate", "like", "%" . $keyword . "%")
                        ->orWhere("se_cate", "like", "%" . $keyword . "%")
                        ->orWhere("th_cate", "like", "%" . $keyword . "%")
                        ->orWhere("last_cate", "like", "%" . $keyword . "%");
                });
            }

            $result = $builder->get();

            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }
        return $returnMsg;
    }

    /****************************************** 카테고리 end **********************************************/

    /****************************************** 이미지 start **********************************************/

    /**
     * @func imgCallBack
     * @description '이미지 콜백'
     * @param array $params
     * @return array
    */
    public function imgCallBack(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $channel_queue_id = $params["data"]["channel_queue_id"];
            $member_id        = $params["data"]["member_id"];
            $images           = $params["data"]["images"];

            $header = array(
                'Content-type: application/json'
            );

            $payload = [
                "channel_queue_id" => $channel_queue_id,
                "member_id"        => $member_id,
                "images"           => $images
            ];
            $endPoint = $this->domain . "/api/w/image/callback";
            $result   = helpers_curl("POST", $endPoint, $header, $payload);

            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        $returnMsg["params"] = $params;
        debug_log(json_encode($returnMsg, JSON_UNESCAPED_UNICODE), "onchannel/imgCallBack", "imgCallBack");

        return $returnMsg;   
    }

    /****************************************** 이미지 end **********************************************/
}
