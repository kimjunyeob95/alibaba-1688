<?php

namespace Tests\Feature;

use App\Constants\SlackConstant;
use App\Packages\Slack;
use Tests\TestCase;

class SlackTest extends TestCase
{
    # php artisan test --filter testSlackSendMessage
    public function testSlackSendMessage()
    {
        $webhookUrl  = SlackConstant::BONAERA_IN_STATUS;
        $slack       = new Slack();
        $message     = "[WMS 입고 알림 Test]\n";
        $message    .= "상태 : 정상입고\n";
        $message    .= "채널 주문번호 : GO_1014142332\n";
        $message    .= "WAPP 주문 번호 : 2354286938502135493\n";
        $message    .= "입고번호 : ST241104000674\n";
        $message    .= "1. 블루 파니니(지퍼 롬퍼)_사이즈 90(82-88cm 권장) (10) : 입고완료\n";
        $message    .= "2. 블루 파니니(지퍼 롬퍼)_사이즈 110(94-100cm 권장) (10): 입고완료\n";

        $result = $slack->sendMessage($webhookUrl, $message);
        dd($result);
        $this->assertTrue($result['isSuccess']);
    }
}
