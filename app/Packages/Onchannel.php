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
use App\Models\ChannelCategoryRegistData;
use App\Models\OnchannelProductDetailLog;
use App\Models\OnchannelProductLog;
use App\Models\OnchCategoryExcelDataCopy2;
use App\Models\ProductData;
use App\Models\ProductModiData;
use App\Models\ProductWeightData;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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
        $endPoint     = $this->domain . "/api/v1/product/regist";

        if( isset($params["sendTypeList"]) ){
            $sendTypeList = $params["sendTypeList"];
        }
        if( isset($params["endPoint"]) ){
            $endPoint = $this->domain . $params["endPoint"];
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

                        $getPrdParams = $this->_getPrdParams($prdObj, $sendType, MallConstant::SEND_TYPE_REGIST);
                        if( $getPrdParams["isSuccess"] === false ){
                            throw new Exception($getPrdParams["msg"]);
                        }
                        $payload = $getPrdParams["data"];

                        $header = array(
                            'Content-type: application/json',
                            'Authorization: Bearer ' . $this->token,
                        );
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

                            $successIds[] = [
                                "offer_id" => $offerId,
                                "prd_code" => $resultCurl["prd_code"]
                            ];
                        } else {
                            $msg = "온채널 통신 에러";
                            if( isset($resultCurl["msg"]) ){
                                $msg = $msg . " " . $resultCurl["msg"];
                            } else {
                                if(is_array($resultCurl)){
                                    $resultCurl = json_encode($resultCurl, JSON_UNESCAPED_UNICODE);
                                }
                                $resultCurl = "offerId: {$offerId} | endPoint: {$endPoint} \r\n" . $resultCurl;
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

                            $failIds[] = [
                                "offer_id" => $offerId,
                                "msg"      => $msg
                            ];
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
                    $modiResult = $this->productModi([$offerId], $sendType);
                    if( $modiResult["isSuccess"] === true && isset($modiResult["data"]["prd_code"]) ){
                        $successIds[] = [
                            "offer_id" => $offerId,
                            "prd_code" => $modiResult["data"]["prd_code"]
                        ];
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

                    $getPrdParams = $this->_getPrdParams($prdObj, $sendType, MallConstant::SEND_TYPE_MODI);
                    if( $getPrdParams["isSuccess"] === false ){
                        throw new Exception($getPrdParams["msg"]);
                    }
                    $payload = $getPrdParams["data"];

                    $header = array(
                        'Content-type: application/json',
                        'Authorization: Bearer ' . $this->token,
                    );

                    $endPoint   = $this->domain . "/api/w/product/edit";
                    $resultCurl = helpers_curl("POST", $endPoint, $header, $payload);
                    if( isset($resultCurl["prd_code"]) && $resultCurl["prd_code"] ){

                        OnchannelProductDetailLog::create([
                            "log_id"     => $logObj->id,
                            "send_type"  => MallConstant::SEND_TYPE_MODI,
                            "is_success" => MallConstant::REGIST_SUCCESS,
                            "message"    => ""
                        ]);

                        $returnMsg = helpers_success_message($resultCurl);
                    } else {
                        $msg = "온채널 통신 에러";
                        if( isset($resultCurl["msg"]) ){
                            $msg = $msg . $resultCurl["msg"];
                        } else {
                            if(is_array($resultCurl)){
                                $resultCurl = json_encode($resultCurl, JSON_UNESCAPED_UNICODE);
                            }
                            $resultCurl = "offerId: {$offerId} | endPoint: {$endPoint} \r\n" . $resultCurl;
                            debug_log($resultCurl, "onchannel/prdModi", "prdModi");
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
        $now      = Carbon::now();
        $modiObjs = ProductModiData::where([
            "is_send" => ProductConstant::IS_SEND_N,
            "channel" => MallConstant::MALL_ONCHANNEL
        ])
        ->groupBy("offer_id", "w_type")
        ->get();

        foreach ($modiObjs as $modiObj) {
            $result = $this->productModi([$modiObj->offer_id], $modiObj->w_type);

            $query = ProductModiData::where("is_send", ProductConstant::IS_SEND_N)
            ->where("created_at", "<=", $now)
            ->where("offer_id", $modiObj->offer_id)
            ->where("channel", MallConstant::MALL_ONCHANNEL)
            ->where("w_type", $modiObj->w_type);

            if( $result["isSuccess"] === true ){
                // 1. 전송 성공 시
                $query->update([
                    "is_send"       => ProductConstant::IS_SEND_Y,
                    "send_dated_at" => Carbon::now()
                ]);
            } else {
                // 2. 전송 에러 시
                $query->update([
                    "is_send" => ProductConstant::IS_SEND_E,
                    "msg"     => $result["msg"]
                ]);
            }
        }
    }

    /**
     * api param 생성 - 상품 등록/수정 공통사용
     *
     * @param Model $prdObj
     * @param int $sendType
     * @param string $mode
     * @return array
     */
    private function _getPrdParams(Model $prdObj, int $sendType, string $mode = MallConstant::SEND_TYPE_REGIST): array
    {
        $return = helpers_fail_message();

        try{
            if( $mode == MallConstant::SEND_TYPE_REGIST ){
                $channelCnt = ChannelCategoryRegistData::where([
                    "channel"     => $this->channel,
                    "send_type"   => $sendType,
                    "category_id" => $prdObj->category_id,
                    "is_regist"   => MallConstant::REGIST_Y,
                ])->count();
    
                if( $channelCnt == 0 ){
                    throw new Exception(MallErrorMessageConstant::getFitErrorMessage("CATEGORY_REGIST"));
                }
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

            $weights = CategoryConstant::WEIGHTS;

            $prdState = OnchannelConstant::STATUS_ON_SALE_NUMBER;
            if( $prdObj->status != ProductConstant::PRD_STATUS_PUBLISH ){
                $prdState = OnchannelConstant::STATUS_OUT_OF_STOCK_NUMBER;
            }

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

            $prd_desc = $prdObj->prd_desc;
            if( $sendType == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                $prd_desc = $prdObj->prd_desc_kr;
            }

            $noticeInfo   = $prdObj->no_except_notices->pluck("attribute_value_kr","attribute_name_kr")->toArray();
            $notice_desc  = getNoticeInfoTable($noticeInfo);
            $prd_desc    .= $notice_desc;

            $prd_desc = "<div style='text-align: center !important'>" . $prd_desc . "</div>";

            $prdImgDesc = "<div><div style='width: 830px; margin:20px auto;'>
            <h5 style='text-align: center; padding: 0px; font-size: 20px; text-align: center; color: #000;font-weight: 900; margin-bottom: 40px;'>상품 이미지</h5>
            <ul style='display: flex; flex-wrap: wrap; justify-content: center;'>";
            foreach ($images as $imgObj) {
                /** html 코드 작성 */
                $prdImgDesc .= "<li style='display: block; width: 138px; height:138px; margin:0 4px 20px 4px;'>
                    <img src='{$imgObj['img_url']}' alt='img' style='width:100%; height:100%;'>
                </li> ";
            }
            $prdImgDesc = $prdImgDesc . "</ul></div>";
            $prd_desc   = $prdImgDesc . $prd_desc . "</div>";

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

            if( $mode == MallConstant::SEND_TYPE_MODI ){
                $payload["prd_code"]  = $prdObj->oc_public_log->prd_code;
                $payload["prd_state"] = $prdState;
            }

            $options = [];
            foreach ($prdObj->no_except_options as $option) {
                $ocPrice = ocPrice($option->price_1688_option, (int)$delivery_price);

                $options[] = [
                    "op_rank"      => "1",
                    "op_code"      => $option->id,
                    "option_nm"    => $option->option_name_kr,
                    "cus_price"    => $ocPrice["cus_price"],
                    "disc_price"   => 0,
                    "option_price" => 0,
                    "vendor_price" => 0,
                    "onch_price"   => $option->price_1688_option,
                    "total_count"  => 0,
                    "weight"       => $option->weight,
                    "volume"       => "",
                    "amount"       => 0
                ];
            }
            $payload["options"] = $options;

            $return = helpers_success_message($payload);
        } catch(Exception $e){
            $return = helpers_fail_message($e->getMessage());
        }

        return $return;
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
