<?php

namespace Tests\Feature;

use Tests\TestCase;

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

    

}
