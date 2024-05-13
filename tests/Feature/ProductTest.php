<?php

namespace Tests\Feature;

use App\Constants\ImageConstant;
use App\Models\CategoryMapping;
use App\Models\ProductData;
use App\Models\ProductForbiddenData;
use App\Models\ProductImageData;
use App\Models\ProductOptionData;
use App\Packages\S3;
use App\Services\GenuioService;
use App\Services\Product\ProductW2;
use App\Vo\Product\Product1688ImageDto;
use Exception;
use Tests\TestCase;
use Illuminate\Support\Facades\File;

class ProductTest extends TestCase
{
    # php artisan test --filter testConvertW1toW2
    public function testConvertW1toW2()
    {
        $productW2 = app(ProductW2::class);
        $productW2->convertW1toW2();
    }

    # php artisan test --filter testPublishedState
    public function testPublishedState()
    {
        ProductData::where([
            "trans_status"   => "Y",
            "mapping_status" => "Y",
        ]);
    }

    # s3 upload
    # php artisan test --filter testS3Upload
    public function testS3Upload()
    {
        $s3 = new S3();
        $offerId = 737834654023; 
        
        $prdObj   = ProductData::where("offer_id", $offerId)->first();
        $dateName = $prdObj->created_at->format('Y/m/d');

        $imgObj = ProductImageData::where([
            "offer_id" => $offerId,
            "img_type" => "main",
        ])->first();
        $mime = pathinfo($imgObj->img_url_origin, PATHINFO_EXTENSION);
        if (preg_match('/^(jpg|jpeg|png|gif)/i', $mime, $matches)) {
            $mime = $matches[0];
        }
        if( $imgObj->img_type == ImageConstant::IMAGE_TYPE_MAIN ){
            $imgName  = "/product/" . $dateName . "/" . $offerId . "_" . $imgObj->img_type . "." . $mime;
        } else {
            $imgName  = "/product/" . $dateName . "/" . $offerId . "_" . $imgObj->id . "_" . $imgObj->img_type . "." . $mime;
        }
        $options = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
        $context = stream_context_create($options);
        $fileContent = file_get_contents($imgObj->img_url_origin, false, $context);
        $imgEncodeBase64 = base64_encode($fileContent);
        $uploadResult    = $s3->uploadFile($imgName, base64_decode($imgEncodeBase64));
        if( $uploadResult == true ) {
            $img_url_trans = env("AWS_URL") . $imgName;
            dd($img_url_trans);
        }
        
    }

    # Genuio img queue create
    # php artisan test --filter testGenuioImgCreate
    public function testGenuioImgCreate()
    {
        $offerId                 = 740275289359;
        $product1688ImageDtoList = [];
        $imgObjs                 = ProductImageData::where("offer_id", $offerId)->get();
        foreach ($imgObjs as $imgObj) {
            $product1688ImageDto = new Product1688ImageDto();
            $product1688ImageDto->bind([
                "offerId"        => $offerId,
                "imgType"        => $imgObj->img_type,
                "is_except"      => $imgObj->is_except,
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

    # php artisan test --filter testProductPrice
    public function testProductPrice()
    {
        $prdObjs = ProductOptionData::groupBy("offer_id")->get();

        foreach ($prdObjs as $prdObj) {
            $price_1688 = $prdObj->price_1688;
            $option_price = round( $price_1688 * env("1688_EXCHANGE_RATE", 200) , -1);  // 1의 자리 반올림

            $option_price_sum = (int)intval($option_price) + intval($option_price * env("OPTION_PRICE_RATE", 0.12));
            $option_price_cal = round($option_price_sum / 10) * 10;
            $onch_price = $option_price_cal;
    
            $recom_cus_price_sum = (int)intval($option_price) + intval($option_price * env("RECOM_CUS_PRICE_RATE", 0.45));
            $recom_cus_price_cal = round($recom_cus_price_sum / 10) * 10;
            $cus_price           = $recom_cus_price_cal;
            $recom_cus_price     = $recom_cus_price_cal;

            ProductOptionData::where("offer_id", $prdObj->offer_id)->update([
                "option_price"    => $option_price,
                "onch_price"      => $onch_price,
                "cus_price"       => $cus_price,
                "recom_cus_price" => $recom_cus_price,
            ]);
        };

        dd("끝");
    }

    # php artisan test --filter testProductName
    public function testProductName()
    {
        $prdObjs = ProductData::get();
        // $prdObjs = ProductForbiddenData::get();

        $filePath     = public_path('app/w_forbidden_word.txt');
        $removeWords  = [];
        $replaceWords = [];
        if (File::exists($filePath)) {
            $lines = File::lines($filePath);
            foreach ($lines as $line) {
                $lineArr = explode(',', $line);

                $type     = $lineArr[0];
                $status   = $lineArr[1];
                $offerId  = $lineArr[2];
                $prevWord = $lineArr[3];
                $nextWord = $lineArr[4];

                if( $status == "검토완료" ){
                    if( $type == "삭제어" ){
                        $removeWords[] = $prevWord;
                    } else if( $type == "교체어" ){
                        $replaceWords[] = [
                            "prevWord" => $prevWord,
                            "nextWord" => $nextWord,
                        ];
                    }
                }
            }
        } else {
            throw new Exception("파일이 존재하지 않습니다.");
        }
        foreach ($prdObjs as $prdObj) {
            $forObj = ProductForbiddenData::where("offer_id", $prdObj->offer_id)->first();

            if( $forObj != null ){
                $prd_name_kr = $forObj->prd_name_trans_origin;
            } else {
                $prd_name_kr = $prdObj->prd_name_kr;
            }

            // 1. 삭제어
            $upText = $this->removeSpecialSequence($prd_name_kr, $removeWords);

            // 2. 교체어
            $upText = $this->replaceWord($upText, $replaceWords);

            $upText = trim($upText);

            if( $prd_name_kr != $upText ){
                ProductForbiddenData::updateOrCreate(
                    ["offer_id" => $prdObj->offer_id],
                    [
                        "prd_name_trans_origin"    => $prd_name_kr,
                        "prd_name_trans_forbidden" => $upText
                    ]
                );
                ProductData::where("id", $prdObj->id)->update([
                    "prd_name_kr" => $upText
                ]);
            }
        };

        dd("끝");
    }

    function removeSpecialSequence(string $text, array $removeWords)
    {
        foreach ($removeWords as $removeWord) {
            // 1. 삭제어 앞과 뒤에 공백이 없는 경우 삭제어만 삭제
            //    예: "HelloWord"에서 "Word"를 삭제 -> "Hello"
            $pattern1 = '/(?<!\s)' . preg_quote($removeWord, '/') . '(?!\s)/';
            if (preg_match($pattern1, $text)) {
                $text = preg_replace($pattern1, '', $text);
            }

            // 2. 삭제어 앞 또는 뒤에 공백이 있는 경우 삭제어만 삭제
            //    예: "Hello Word "에서 "Word"를 삭제 -> "Hello "
            $pattern2 = '/(?<=\s)' . preg_quote($removeWord, '/') . '(?!\s)|(?<!\s)' . preg_quote($removeWord, '/') . '(?=\s)/';
            if (preg_match($pattern2, $text)) {
                $text = preg_replace($pattern2, '', $text);
            }

            // 3. 삭제어 앞과 뒤에 공백이 있는 경우 하나의 공백과 삭제어만 삭제
            //    예: "Hello Word Test"에서 "Word"를 삭제 -> "Hello Test"
            $pattern3 = '/\s+' . preg_quote($removeWord, '/') . '\s+/';
            if (preg_match($pattern3, $text)) {
                $text = preg_replace($pattern3, ' ', $text);
            }
        }
        return $text;
    }

    function replaceWord(string $text, array $replaceWords)
    {
        foreach ($replaceWords as $replaceWordArr) {
            $originWord  = $replaceWordArr["prevWord"];
            $replaceWord = $replaceWordArr["nextWord"];

            // 1. 해당 텍스트가 originWord에 걸릴 시 replaceWord로 교체
            $text = str_replace($originWord, $replaceWord, $text);
    
        }
        return $text;
    }
}
