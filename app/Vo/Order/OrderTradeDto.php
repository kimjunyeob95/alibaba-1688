<?php

namespace App\Vo\Order;

use App\Vo\Vo;
use Carbon\Carbon;

class OrderTradeDto extends Vo
{
    /** 주문ID */
    protected string $order_id = "";
    /** 단계ID */
    protected int $phase = 0;
    /** payWay 설명 */
    protected string $pay_way_desc = "";
    /** 익스프레스페이 */
    protected bool $express_pay = false;
    /** 급여시간 */
    protected ?string $pay_time = null;
    /** 지불상태설명 */
    protected string $pay_status_desc = "";
    /** 페이웨이 */
    protected string $pay_way = "";
    /** 카드페이 */
    protected bool $card_pay = false;
    /** 지불 상태 */
    protected string $pay_status = "";
    /** 단계금액 */
    protected float $phas_amount = 0.0;

    public function bind(mixed $data): void
    {
        $this->order_id        = $data["orderId"];
        $this->phase           = $data["phase"] ?? 0;
        $this->pay_way_desc    = $data["payWayDesc"] ?? "";
        $this->express_pay     = $data["expressPay"] ?? false;
        $this->pay_time        = $data["payTime"] ?? null;
        $this->pay_status_desc = $data["payStatusDesc"] ?? "";
        $this->pay_way         = $data["payWay"] ?? "";
        $this->card_pay        = $data["cardPay"] ?? false;
        $this->pay_status      = $data["payStatus"] ?? "";
        $this->phas_amount     = $data["phasAmount"] ?? 0.0;

        $this->__changeUTCTime();
    }

    public function __changeUTCTime()
    {
        if ($this->pay_time) {
            $this->pay_time = Carbon::createFromFormat('YmdHisvO', $this->pay_time);
        }
    }
}