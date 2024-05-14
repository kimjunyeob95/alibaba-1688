<?php

namespace Tests\Feature;

use App\Constants\GosiConstants;
use App\Models\OnchannelProductLog;
use App\Models\ProductData;
use Carbon\Carbon;
use Exception;
use Tests\TestCase;

class OnchannelTest extends TestCase
{

    # 온채널 관리자 상품 등록
    # php artisan test --filter testOnchProductCreate
    public function testOnchProductCreate()
    {
        $token    = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJtZW1iZXJfaWQiOiJvbmNoMTY4OCIsIm1tYnJfdHlwZSI6Im9uY2htYW4iLCJ0aW1lc3RhbXAiOjQ4NjkxODE5ODJ9.AijywuhAP6ZkxySsZWOqEU-ID8XoesePcm8lSB1w1rw";
        $endPoint = "https://task.onch3.co.kr/api/v1/product/regist";

        $getPrdObjs = ProductData::with([
            "main_img",
            "no_except_sub_imgs",
            "extends",
            "options",
            "notices",
            "category",
            "w_mapping"
        ])
        ->where("status", "!=", "miss")
        ->where([
            
        ])
        ->get();
        foreach ($getPrdObjs as $getPrdObj) {
            
            $ocObj = OnchannelProductLog::where([
                "offer_id"       => $getPrdObj->offer_id,
                "regist_success" => "Y",
            ])->first();

            if( $ocObj != null ) continue;

            $prd_desc    = $getPrdObj->prd_desc;
            $noticeInfo  = $getPrdObj->notices->where("is_except",GosiConstants::IS_EXCEPT_N)->pluck("attribute_value_kr","attribute_name_kr")->toArray();
            $notice_desc = getNoticeInfoTable($noticeInfo);
            $prd_desc .= $notice_desc;

            $payload     = [
                "nat_sec"         => "KR",
                "supp_sec"        => 3,
                "jejo_code"       => $getPrdObj->offer_id,
                "product_nm"      => $getPrdObj->prd_name_kr,
                "prd_char1"       => $getPrdObj->minor_not_sale,
                "trans_info"      => "오후5시/온채널/7일 이상 소요",
                "send_check"      => 1,
                "send_price"      => (int)0,
                "jeju_send_price" => (int)$getPrdObj->extends->send_jeju_price,
                "etc_send_price"  => (int)$getPrdObj->extends->send_etc_price,
                "trans_nm"        => "대한통운",
                "prd_channel"     => 30,
                "sale_num"        => 3,
                "etc_comment"     => "해외배송 상품 입니다.",
                "return_comment"  => $getPrdObj->return_comment,
                "sec_tax"         => "N",
                "subject"         => $getPrdObj->prd_name_kr,
                "contents"        => $prd_desc,
                "img_url"         => $getPrdObj->main_img->img_url_origin,
                "img_nm_550"      => $getPrdObj->no_except_sub_imgs[0]->img_url_origin,
                "img_nm_300"      => $getPrdObj->no_except_sub_imgs[1]->img_url_origin,
                "img_nm_130"      => $getPrdObj->no_except_sub_imgs[2]->img_url_origin,
                "cate_num"        => 26,
                "store_code"      => (string)$getPrdObj->w_mapping->mapping_code,
                "brand_info" => [
                    "brand_nm"                 => "온채널X1688",
                    "release_zipcode"          => "06158",
                    "release_address"          => "서울특별시 강남구 테헤란로79길",
                    "release_address_detail"   => "11-1",
                    "release_phone"            => "01012345678",
                    "release_mobile"           => "01012345678",
                    "return_zipcode"           => "06158",
                    "return_address"           => "서울특별시 강남구 테헤란로79길",
                    "return_address_detail"    => "11-1",
                    "return_phone"             => "01012345678",
                    "return_mobile"            => "01012345678",
                    "basic_delivery_charge"    => 3000,
                    "jeju_delivery_charge"     => 5000,
                    "extra_delivery_charge"    => 5000,
                    "return_delivery_charge"   => 3000,
                    "exchange_delivery_charge" => 3000,
                    "delivery_id"              => 4
                ]
            ];

            $options = [];
            foreach ($getPrdObj->options as $option) {
                $options[] = [
                    "op_rank"         => "1",
                    "option_nm"       => $option->option_name_kr,
                    "cus_price"       => (int)$option->cus_price + 12000,
                    "disc_price"      => 0,
                    "option_price"    => 0,
                    "vendor_price"    => 0,
                    "onch_price"      => (int)$option->option_price + 12000,
                    "total_count"     => 0,
                    "weight"          => $option->weight,
                    "volume"          => "",
                    "amount"          => 0
                ];
            }
            $payload["options"] = $options;
            $header = array(
                'Content-type: application/json',
                'Authorization: Bearer '.$token,
            );
            $result = helpers_curl("POST", $endPoint, $header, $payload);
            
            try {
                if( $result["isSuccess"] == true ){
                    OnchannelProductLog::updateOrCreate(
                        [
                            "offer_id"       => $getPrdObj->offer_id,
                            "member_id"      => "onch1688",
                            "prd_code"       => $result["prd_code"],
                        ],
                        [
                            "regist_success" => "Y",
                            "message"        => "",
                            "registed_at"      => Carbon::now(),
                        ]
                    );
                } else {
                    OnchannelProductLog::updateOrCreate(
                        [
                            "offer_id"       => $getPrdObj->offer_id,
                            "member_id"      => "onch1688",
                        ],
                        [
                            "prd_code"       => 0,
                            "regist_success" => "N",
                            "message"        => $result["msg"],
                        ]
                    );
                }
            } catch (Exception $e) {
                OnchannelProductLog::updateOrCreate(
                    [
                        "offer_id"       => $getPrdObj->offer_id,
                        "member_id"      => "onch1688",
                    ],
                    [
                        "prd_code"       => 0,
                        "regist_success" => "N",
                        "message"        => $e->getMessage(),
                    ]
                );
            }
            
            
        }
        dd("끝");

        // try {
        //     $this->assertEquals(helpersSuccessMessage(), $result);
        // } catch (Exception $e) {
        //     dd($result);
        // }
    }
}
