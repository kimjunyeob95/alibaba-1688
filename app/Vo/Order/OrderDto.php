<?php

namespace App\Vo\Order;

use App\Vo\Vo;

class OrderDto extends Vo
{
    protected string $order_id               = "";
    protected int $offer_id                  = 0;
    protected string $buyer_name             = "";
    protected string $buyer_clearance_number = "";
    protected string $buyer_number           = "";
    protected string $buyer_phone            = "";
    protected string $buyer_zipcode          = "";
    protected string $buyer_address          = "";
    protected string $buyer_memo             = "";
    protected int $total_quantity            = 0;
    protected float $total_price             = 0.0;

    public function bind(mixed $data): void
    {
        $baseInfo = $data["baseInfo"] ?? [];

        if( !empty($baseInfo) ){
            $this->order_id            = $data["id"];
            $this->status              = $data["status"];
            $this->all_delivered_time  = $data["allDeliveredTime"] ?? null;
            $this->pay_time            = $data["payTime"] ?? null;
            $this->discount            = $data["discount"] ?? 0;
            $this->sum_product_payment = $data["sumProductPayment"] ?? 0;
            $this->modify_time         = $data["modifyTime"] ?? null;
            $this->close_reason        = $data["closeReason"] ?? "";
            $this->complete_time       = $data["completeTime"] ?? null;
            $this->close_operate_type  = $data["closeOperateType"] ?? "";
            $this->total_amount        = $data["totalAmount"] ?? 0;
            $this->seller_id           = $data["sellerID"] ?? "";
            $this->shipping_fee        = $data["shippingFee"] ?? 0;
            $this->refund              = $data["refund"] ?? 0;
            $this->refund_payment      = $data["refundPayment"] ?? 0;
            $this->refund_status       = $data["refundStatus"] ?? 0;
        }

        $this->offer_id               = $data["offerId"];
        $this->channel                = $data["channel"];
        // $this->buyer_name             = $data["buyer_name"];
        // $this->buyer_clearance_number = $data["buyer_clearance_number"];
        // $this->buyer_number           = $data["buyer_number"];
        // $this->buyer_phone            = $data["buyer_phone"];
        // $this->buyer_zipcode          = $data["buyer_zipcode"];
        // $this->buyer_address          = $data["buyer_address"];
        // $this->buyer_memo             = $data["buyer_memo"];
        // $this->total_quantity         = $data["total_quantity"];
        // $this->total_price            = $data["total_price"];
    }
}