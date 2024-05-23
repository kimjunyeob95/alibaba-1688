<?php

namespace App\Packages;

use App\Abstracts\MallApiAbstract;
use App\Abstracts\OrderAbstract;
use App\Abstracts\TransApiAbstract;
use App\Constants\ImageConstant;
use App\Constants\MallConstant;
use App\Constants\MallErrorMessageConstant;
use App\Constants\OnchannelConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Models\CategoryMapping;
use App\Models\OnchannelProductLog;
use App\Models\ProductData;
use App\Models\ProductModiData;
use Carbon\Carbon;
use Exception;
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
        TransApiAbstract $transApiAbstract
    )
    {
        parent::__construct($jwtPackage, $channel, $orderW1, $transApiAbstract);
        $this->token  = env("ON_TOKEN");
        $this->domain = env("OC_DOMAIN", "https://task.onch3.co.kr");
    }

    /**
     * @func productRegist
     * @description '상품등록'
     * @param array $offerIds
     * @param string $type
     * @return array
    */
    public function productRegist(array $offerIds, string $type = WConstant::WAPP_W1): array
    {
        $successIds = [];
        $failIds    = [];
        $updateIds  = [];

        foreach ($offerIds as $offerId) {
            $regCnt = OnchannelProductLog::where([
                "offer_id"       => $offerId,
                "member_id"      => OnchannelConstant::ONCH1688,
                "regist_success" => MallConstant::REGIST_SUCCESS
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
                        "w_mapping"
                    ])->where("offer_id", $offerId)->first();

                    if( $prdObj == null ){
                        throw new Exception(MallErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));
                    }

                    if(count($prdObj->no_except_sub_imgs) < 3){
                        throw new Exception(MallErrorMessageConstant::getNotHaveErrorMessage("IMAGES"));
                    }

                    $prd_desc     = $prdObj->prd_desc;
                    $noticeInfo   = $prdObj->notices->pluck("attribute_value_kr","attribute_name_kr")->toArray();
                    $notice_desc  = getNoticeInfoTable($noticeInfo);
                    $prd_desc    .= $notice_desc;
        
                    $images = [];
                    foreach ($prdObj->images as $imgObj) {
                        if( $imgObj->is_except == ImageConstant::IS_EXCEPT_N && $imgObj->lang == WConstant::WAPP_KR ){
                            $images[] = [
                                "img_type" => $imgObj->img_type,
                                "img_url"  => $imgObj->img_url_origin
                            ];
                        }
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
                        "prd_channel"     => OnchannelConstant::PRD_CHANNEL,
                        "sale_num"        => OnchannelConstant::SALE_NUM,
                        "etc_comment"     => OnchannelConstant::ETC_COMMENT,
                        "return_comment"  => $prdObj->return_comment,
                        "sec_tax"         => OnchannelConstant::SEC_TAX,
                        "subject"         => $prdObj->prd_name_kr,
                        "contents"        => $prd_desc,
                        "img_url"         => $prdObj->main_img->img_url_origin,
                        "img_nm_550"      => $prdObj->no_except_sub_imgs[0]->img_url_origin,
                        "img_nm_300"      => $prdObj->no_except_sub_imgs[1]->img_url_origin,
                        "img_nm_130"      => $prdObj->no_except_sub_imgs[2]->img_url_origin,
                        "min_count"       => $prdObj->start_quantity,
                        "images"          => $images,
                        "cate_num"        => 26,
                        "store_code"      => (string)$prdObj->w_mapping->mapping_code,
                        "brand_info"      => OnchannelConstant::BRAND_INFO
                    ];
        
                    $options = [];
                    foreach ($prdObj->options as $option) {
                        $options[] = [
                            "op_rank"      => "1",
                            "op_code"      => $option->id,
                            "option_nm"    => $option->option_name_kr,
                            "cus_price"    => (int)$option->cus_price + 12000,
                            "disc_price"   => 0,
                            "option_price" => 0,
                            "vendor_price" => 0,
                            "onch_price"   => (int)$option->option_price + 12000,
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

                    $endPoint = $this->domain . "/api/v1/product/regist";
                    $result = helpers_curl("POST", $endPoint, $header, $payload);

                    if( isset($result["prd_code"]) && $result["prd_code"] ){
                        OnchannelProductLog::updateOrCreate(
                            [
                                "offer_id"       => $offerId,
                                "member_id"      => OnchannelConstant::ONCH1688,
                            ],
                            [
                                "prd_code"       => $result["prd_code"],
                                "regist_success" => MallConstant::REGIST_SUCCESS,
                                "message"        => "",
                                "registed_at"      => Carbon::now(),
                            ]
                        );
                        $successIds[] = $offerId;
                    } else {
                        OnchannelProductLog::updateOrCreate(
                            [
                                "offer_id"       => $offerId,
                                "member_id"      =>OnchannelConstant::ONCH1688,
                            ],
                            [
                                "prd_code"       => 0,
                                "regist_success" => MallConstant::REGIST_FAIL,
                                "message"        => $result["msg"],
                            ]
                        );
                    }
                } catch (Exception $e) {
                    $failIds[] = [
                        "offer_id" => $offerId,
                        "msg"      => $e->getMessage()
                    ];
                }

                sleep(1);
            } else {
                $updateIds[] = $offerId;
            }
        }

        $result = ["success" => $successIds, "fail" => $failIds];

        return helpers_success_message($result);
    }

    /**
     * @func productModi
     * @description '상품수정'
     * @param array $offerIds
     * @param string $type
     * @return array
    */
    public function productModi(array $offerIds, string $type):array
    {
        $successIds = [];
        $failIds    = [];

        foreach ($offerIds as $offerId) {

        }

        $result = ["success" => $successIds, "fail" => $failIds];

        return helpers_success_message($result);
    }

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
                        //이지셀 카테고리 매핑
                        CategoryMapping::updateOrCreate([
                                "category_id"     => $cate->category_id,
                                "mapping_channel" => MallConstant::MALL_ONCHANNEL
                            ],[
                                "mapping_code" => $data[1]
                            ]);

                        //이지셀 해외카테고리 매핑
                        CategoryMapping::updateOrCreate([
                                "category_id"     => $cate->category_id,
                                "mapping_channel" => MallConstant::MALL_ONCHANNEL
                            ],[
                                "mapping_code" => $data[2]
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
     * @func sendModiProduct
     * @description '수정 된 상품 전송'
     * @return void
     */
    public function sendModiProduct(): void
    {
        $now      = Carbon::now();
        $modiObjs = ProductModiData::where("is_send", ProductConstant::IS_SEND_N)
        ->where("channel", MallConstant::MALL_ONCHANNEL)
        ->groupBy("offer_id")
        ->get();
        
    }

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
            $channel_queue_id = $params["channel_queue_id"];
            $member_id        = $params["member_id"];
            $images           = $params["images"];

            $header = array(
                'Content-type: application/json'
            );

            $payload = [
                "channel_queue_id" => $channel_queue_id,
                "member_id"        => $member_id,
                "images"           => $images
            ];
            $endPoint = $this->domain . "/api/w/image/callback";

            $result = helpers_curl("POST", $endPoint, $header, $payload);

            debug_log(json_encode($result, JSON_UNESCAPED_UNICODE), "onchannel/imgCallBack", "imgCallBack");

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
        
    }
}
