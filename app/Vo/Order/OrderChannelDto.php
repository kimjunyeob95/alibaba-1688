<?php

namespace App\Vo\Order;

use App\Vo\Vo;

class OrderChannelDto extends Vo
{
    /** 주문ID */
    protected string $order_id = "";
    /** 채널 주문ID */
    protected string $channel_order_id = "";
    /** 제품ID */
    protected int $offer_id = 0;
    /** 구매자명 */
    protected string $buyer_name = "";
    /** 구매자 개인통관번호 */
    protected string $buyer_clearance_number = "";
    /** 구매자 전화번호 */
    protected string $buyer_number = "";
    /** 구매자 핸드폰번호 */
    protected string $buyer_phone = "";
    /** 구매자 우편번호 */
    protected string $buyer_zipcode = "";
    /** 구매자 주소 */
    protected string $buyer_address = "";
    /** 배송 요청사항 */
    protected string $buyer_memo = "";

    public function bind(mixed $data): void
    {
        $this->order_id               = $data["orderId"];
        $this->channel_order_id       = $data["channelOrderId"];
        $this->offer_id               = $data["offerId"];
        $this->buyer_name             = $data["buyerName"];
        $this->buyer_clearance_number = $data["buyerClearanceNumber"];
        $this->buyer_number           = $data["buyerNumber"];
        $this->buyer_phone            = $data["buyerPhone"];
        $this->buyer_zipcode          = $data["buyerZipcode"];
        $this->buyer_address          = $data["buyerAddress"];
        $this->buyer_memo             = $data["buyerMemo"];
    }
}