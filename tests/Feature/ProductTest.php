<?php

namespace Tests\Feature;

use App\Models\ProductData;
use App\Models\ProductImageData;
use App\Packages\Connect\Connect;
use App\Services\GenuioService;
use App\Services\OrderService;
use App\Vo\Connect\Order\OrderSubJobDto;
use App\Vo\Product\Product1688ImageDto;
use Carbon\Carbon;
use Exception;
use Tests\TestCase;

class ProductTest extends TestCase
{

    # 온채널 관리자 상품 등록
    # php artisan test --filter testOnchProductCreate
    public function testOnchProductCreate()
    {
        $token    = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJtZW1iZXJfaWQiOiJzZWxsZXJodWJ0ZXN0IiwibW1icl90eXBlIjoib25jaG1hbiIsInRpbWVzdGFtcCI6NDg2NDY3MjczM30.kt-U_cxFmeBQIlsXui75jRQKVBLduXxQ8BpEIvjCX2E";
        $endPoint = "http://192.168.10.191:8084/api/v1/product/regist";

        $getPrdObjs = ProductData::with([
            "main_img",
            "sub_imgs",
            "extends",
            "options",
            "category",
            "oc_mapping"
        ])->get();
        foreach ($getPrdObjs as $getPrdObj) {
            $payload = [
                "nat_sec"         => "KR",
                "supp_sec"        => 1,
                "jejo_code"       => $getPrdObj->offer_id,
                "product_nm"      => $getPrdObj->prd_name_trans,
                "prd_char1"       => $getPrdObj->minor_not_sale,
                "trans_info"      => "오후5시/온채널/7일 이상 소요",
                "send_check"      => 1,
                "send_price"      => (int)$getPrdObj->extends->send_default_price,
                "jeju_send_price" => (int)$getPrdObj->extends->send_jeju_price,
                "etc_send_price"  => (int)$getPrdObj->extends->send_etc_price,
                "trans_nm"        => "대한통운",
                "prd_channel"     => 22,
                "sale_num"        => 3,
                "etc_comment"     => "특이사항입니다.",
                "return_comment"  => "반품 / 교환시 공급사에서 직접 수거접수하는 업체입니다. 단순변심으로 인한 반품시 왕복배송비 6,000원입니다.",
                "sec_tax"         => "N",
                "subject"         => $getPrdObj->prd_name_trans,
                "contents"        => $getPrdObj->prd_desc,
                "img_url"         => $getPrdObj->main_img->img_url_origin,
                "img_nm_550"      => $getPrdObj->sub_imgs[0]->img_url_origin,
                "img_nm_300"      => $getPrdObj->sub_imgs[1]->img_url_origin,
                "img_nm_130"      => $getPrdObj->sub_imgs[2]->img_url_origin,
                "cate_num"        => 26,
                "store_code"      => (string)$getPrdObj->oc_mapping->mapping_code,
                "brand_info" => [
                    "brand_nm"                 => "나이키",
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
                    "basic_delivery_charge"    => 2500,
                    "jeju_delivery_charge"     => 3000,
                    "extra_delivery_charge"    => 5000,
                    "return_delivery_charge"   => 3000,
                    "exchange_delivery_charge" => 3000,
                    "delivery_id"              => 4
                ]
            ];

            $options = [];
            foreach ($getPrdObj->options as $option) {
                $options[] = [
                    "op_rank"      => "1",
                    "option_nm"    => $option->option_name_trans,
                    "cus_price"    => (int)$option->cus_price,
                    "disc_price"   => 0,
                    "option_price" => (int)$option->option_price,
                    "vendor_price" => 0,
                    "onch_price"   => (int)$option->onch_price,
                    "total_count"  => 0,
                    "weight"       => 0,
                    "volume"       => "",
                    "amount"       => 0
                ];
            }
            $payload["options"] = $options;
            
            $header = array(
                'Content-type: application/json',
                'Authorization: Bearer '.$token,
            );
            $result = helpers_curl("POST", $endPoint, $header, $payload);
            
            debug_log(json_encode($result, JSON_UNESCAPED_UNICODE), "test", "test");
        }


        // try {
        //     $this->assertEquals(helpersSuccessMessage(), $result);
        // } catch (Exception $e) {
        //     dd($result);
        // }
    }

    # testCode
    # php artisan test --filter testCode
    public function testCode()
    {
        $offerId = 684130604130; 
        
        $prdObj  = ProductData::where("offer_id", $offerId)->first();
        $imgObjs = ProductImageData::where("offer_id", $offerId)->get();
        $prd_desc_trans = $prdObj->prd_desc;
        foreach ($imgObjs as $imgObj) {
            $mime    = pathinfo($imgObj->img_url_origin, PATHINFO_EXTENSION);
            // 정규 표현식 매치를 사용하여 결과를 검색
            if (preg_match('/^(jpg|jpeg|png|gif)/i', $mime, $matches)) {
                $mime = $matches[0];
            }
            var_dump($mime);
        }
    }

    # Genuio img queue create
    # php artisan test --filter testGenuioImgCreate
    public function testGenuioImgCreate()
    {
        $offerId                 = 773387095350;
        $product1688ImageDtoList = [];
        $imgObjs                 = ProductImageData::where("offer_id", $offerId)->get();
        foreach ($imgObjs as $imgObj) {
            $product1688ImageDto = new Product1688ImageDto();
            $product1688ImageDto->bind([
                "offerId"        => $offerId,
                "imgType"        => $imgObj->img_type,
                "img_url_origin" => $imgObj->img_url_origin,
                "img_url_trans"  => "",
                "isChangeImg"    => true,
                "width"          => 800,
                "height"         => 800,
                "byte"           => 8,
                "mime"           => "image/jpeg"
            ]);
            $product1688ImageDtoList[] = $product1688ImageDto;
        }

        $geService = app(GenuioService::class);
        $geService->createTransProductImg($product1688ImageDtoList, $offerId);
    }

}
