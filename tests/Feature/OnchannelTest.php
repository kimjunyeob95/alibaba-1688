<?php

namespace Tests\Feature;

use App\Constants\GosiConstants;
use App\Models\OnchannelProductLog;
use App\Models\ProductData;
use App\Packages\Onchannel;
use Carbon\Carbon;
use Exception;
use Tests\TestCase;
use Illuminate\Pagination\Paginator;

class OnchannelTest extends TestCase
{

    # 온채널 관리자 상품 등록
    # php artisan test --filter testOnchProductCreate
    public function testOnchProductCreate()
    {
        $onchannel = app(Onchannel::class);

        $offerIds = [524295115827,555898029255,558400786839,573899340149,580790778795,599378332362,601822160930,602461327264,610184622557,616419326098,616458841809,620548920825,626913575008,627313561940,636533590279,638409698605,638674054474,639004299624,642140213182,647655220491,649847584101,651422542293,651811503782,654433064126,655777561112,656438165805,658057810287,666889224452,668697582415,669410666483,669793416039,670263976770,670525216470,671013281104,671718555994,671835691302,672133300699,672345921436,672699245583,673174925382,673452989821,674123071464,677667283988,678420600494,679897174012,680722607841,681620545898,681892861693,688093618672,689573401714,692183148025,692665455615,693489379427,694962639682,697750999874,700561160565,701300034528,701322369387,702077176397,702935939455,703290299697,703348780455,708418255546,718498167458,720091521204,727176894704,729869727779,730566744470,731960880462,733090625275,734017840064,735159754991,739341645876,740055061801,741948308713,742792654987,745838076346,751315694283,762123162819,766147878380,768862179220,769113602604,770336710237,772569170758,772665701844,775111097680,786231256846,788057415246,795903519531];
        $onchannel->productRegist($offerIds);
        dd("끝");
    }

    # 온채널 관리자 상품 등록
    # php artisan test --filter testOnchProductModi
    public function testOnchProductModi()
    {
        $token    = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJtZW1iZXJfaWQiOiJvbmNoMTY4OCIsIm1tYnJfdHlwZSI6Im9uY2htYW4iLCJ0aW1lc3RhbXAiOjQ4NjkxODE5ODJ9.AijywuhAP6ZkxySsZWOqEU-ID8XoesePcm8lSB1w1rw";
        $endPoint = "https://task.onch3.co.kr/api/w/product/edit";

        $getPrdObjs = ProductData::with([
            "images",
            "options",
        ])
        ->select(["product_datas.*", "b.prd_code"])
        ->join("onchannel_product_logs as b","product_datas.offer_id", "=", "b.offer_id")
        ->where("b.regist_success", "Y");

        $perPage = 900;

        $totalCount = $getPrdObjs->count();
        $totalPages = ceil($totalCount / $perPage);

        debug_log("실행", "onchannel", "modiOnchannel");

        for ($page = 1; $page <= $totalPages; $page++) {
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        
            // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
            $pagedData = $getPrdObjs->paginate($perPage);
            $results   = $pagedData->items();

            $msg = "(" . $page . "/" . $totalPages. ") prdCnt: " . count($results) . " 실행시작";
            debug_log($msg, "onchannel", "modiOnchannel");

            foreach ($results as $prdObj) {
                try {
                    $offer_id = $prdObj->offer_id;

                    $payload = [
                        "prd_code"  => $prdObj->prd_code,
                        "min_count" => $prdObj->start_quantity,
                    ];
        
                    $images = [];
                    foreach ($prdObj->images as $img) {
                        if( $img->is_except == "Y" && $img->lang != "kr") continue;
                        $images[] = [
                            "img_type" => $img->img_type,
                            "img_url"  => $img->img_url_origin,
                        ];
                    }

                    $payload["images"] = $images;

                    $options = [];
                    foreach ($prdObj->options as $option) {
                        $options[] = [
                            "op_code"   => $option->id,
                            "option_nm" => $option->option_name_kr,
                        ];
                    }
                    $payload["options"] = $options;

                    $header = array(
                        'Content-type: application/json',
                        'Authorization: Bearer '.$token,
                    );

                    $result = helpers_curl("POST", $endPoint, $header, $payload);
                    
                    if( !isset($result["isSuccess"]) || $result["isSuccess"] != true ){
                        $res = [
                            "offer_id" => $offer_id,
                            "prd_code" => $prdObj->prd_code,
                            "result"   => $result
                        ];
                        debug_log(json_encode($res, JSON_UNESCAPED_UNICODE), "onchannel", "modiOnchannel");
                    }
                } catch (Exception $e) {
                    $res = [
                        "offer_id" => $offer_id,
                        "prd_code" => $prdObj->prd_code,
                        "error"    => $e->getMessage()
                    ];
                    debug_log(json_encode($res, JSON_UNESCAPED_UNICODE), "onchannel", "modiOnchannel");
                }

                sleep(1);
            }
        }
        

        debug_log("종료", "onchannel", "modiOnchannel");
    }

    # 온채널 이미지 콜백
    # php artisan test --filter testOnchCallBackImg
    public function testOnchCallBackImg()
    {
        $domain = env("OC_DOMAIN", "https://task.onch3.co.kr");

        $header = array(
            'Content-type: application/json'
        );

        $payload = [
            "channel_queue_id" => 1,
            "member_id"        => "tester123",
        ];

        $payload["images"][] = [
            "id"             => 1,
            "origin_url"     => "https://naver.png",
            "translated_url" => "https://onch-1688.s3.ap-northeast-2.amazonaws.com/onchannel/test123/product/20240523_143849_1.png",
            "error"          => ""
        ];
        $endPoint = $domain . "/api/w/image/callback";
        $result   = helpers_curl("POST", $endPoint, $header, $payload);
        dd($result);
    }
}
