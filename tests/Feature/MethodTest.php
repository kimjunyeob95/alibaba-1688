<?php

namespace Tests\Feature;

use App\Packages\S3;
use Exception;
use Tests\TestCase;
use Illuminate\Support\Facades\File;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Request;

class MethodTest extends TestCase
{

    # php artisan test --filter testMethodCode
    public function testMethodCode()
    {
        $htmlContent = '<div id="offer-template-0"></div><div style="width: 790.0px;">
         <img src="https://onch-1688.s3.ap-northeast-2.amazonaws.com/product/2024/04/19/711392860000_227078_desc.jpg" style="display: block;width: 100.0%;height: auto;" usemap="#_sdmap_0"/>
        <div class="rich-text-component" style="width: 395.0px;padding: 10.0px;word-break: break-all;white-space: break-spaces;font-family: ali-webfont;zoom: 2;font-size: 0.0px;box-sizing: border-box;">
        <p style="text-align: center;"><span style="font-size: 14.0px;">浅色上衣有一点点微透哈</span></p><p style="text-align: center;"><span style="font-size: 14.0px;">只要穿肉色内衣就可以啦</span></p><p style="text-align: center;"><span style="font-size: 14.0px;">或者加个裹胸哟</span></p></div>
         <img src="https://onch-1688.s3.ap-northeast-2.amazonaws.com/genuio/ai-img/2024/04/19/711392860000_25048_.jpg" style="display: block;width: 100.0%;height: auto;" usemap="#_sdmap_2"/>';

        // 정규 표현식을 사용하여 중국어 문자를 제거
        $cleanHtml = preg_replace('/[\x{4e00}-\x{9fff}]+/u', '', $htmlContent);

        dd($cleanHtml);
    }

    # php artisan test --filter testBase64ImgUpload
    public function testBase64ImgUpload()
    {
        $filePath = public_path('app/base64.txt');
        if (!File::exists($filePath)) {
            throw new Exception("파일이 존재하지 않습니다.");
        }
        $fileContents = File::get($filePath);
        $decodedFile = base64_decode($fileContents);

        $imgName = "/test/dev-1.jpeg";

        $s3 = new S3();
        $uploadResult = $s3->uploadFile($imgName, $decodedFile);

        $img_url_trans = env("AWS_URL") . $imgName;

        dd($uploadResult, $img_url_trans);
    }

    # php artisan test --filter testImgUpload
    public function testImgUpload()
    {
        $img_url_origin = "https://cbu01.alicdn.com/img/ibank/2019/441/080/10439080144_1843455893.jpg";
        $fileContent = fileContents($img_url_origin);
        dd($fileContent);
    }

    # php artisan test --filter testMultiCurl
    public function testMultiCurl()
    {
        $endPoint = "http://127.0.0.1/api/w/1688/message";

        $client     = new Client();
        $postFields = '_aop_signature=65F2AA13AE9C9C9DFE8682EFB90B32F37CB01BE1&message={"bizKey":"2251947542517135493","data":{"buyerMemberId":"b2b-221741213935443542","currentStatus":"waitsellersend","orderId":2237938826932135493,"sellerMemberId":"b2b-22121303457002f54a","msgSendTime":"2024-08-09 09:44:24"},"gmtBorn":1723167864904,"msgId":92705609220,"type":"ORDER_BUYER_VIEW_ORDER_PAY","userInfo":"b2b-221741213935443542"}';

        $promises = [];

        for ($i = 0; $i < 2; $i++) {
            $headers = [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ];
            $request = new Request('POST', $endPoint, $headers, $postFields);
            $promises[] = $client->sendAsync($request);
        }

        // 모든 요청을 병렬로 실행하고 결과를 기다림
        $results = Promise\Utils::settle($promises)->wait();

        // 결과 검증
        $this->assertCount(2, $results);
    }

}
