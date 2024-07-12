<?php

namespace App\Vo\Order;

use App\Vo\Vo;

class OrderChannelDetailDto extends Vo
{
    /** order_channel_datas id */
    protected int $order_channel_id = 0;
    /** product_option_datas id */
    protected int $option_id = 0;
    /** product_option_datas price_1688_origin DB에 저장된 가격(위안) */
    protected float $origin_price = 0.0;
    /** 채널에서 발주신청한 옵션 가격 */
    protected float $channel_price = 0.0;
    /** 수량 */
    protected int $quantity = 0;

    public function bind(mixed $data): void
    {
        $this->order_channel_id = $data["orderChannelId"];
        $this->option_id        = $data["optionId"];
        $this->origin_price     = $data["originPrice"];
        $this->channel_price    = $data["channelPrice"];
        $this->quantity         = $data["quantity"];
    }
}