<?php

namespace App\Vo\Order;

use App\Vo\Vo;
use Carbon\Carbon;

class OrderBaseDto extends Vo
{
    /** 주문ID */
    protected string $order_id = "";
    /** 제품ID */
    protected int $offer_id = 0;
    /** 채널명 */
    protected string $channel = "";
    /** 주문상태 */
    protected string $status = "";
    /** 주문처리 완료 시간(송장입력) */
    protected ?string $all_delivered_time = null;
    /** 결제시간 */
    protected ?string $pay_time = null;
    /** 할인 */
    protected float $discount = 0.0;
    /** 총 제품 금액 */
    protected float $sum_product_payment = 0.0;
    /** 수정시간 */
    protected ?string $modify_time = null;
    /** 주문 취소 사유 */
    protected string $close_reason = "";
    /** 거래종료 시간 */
    protected ?string $complete_time = null;
    /** 거래종료사유 */
    protected string $close_operate_type = "";
    /** 지불 할 총액 : 제품금액 + 배송비 */
    protected float $total_amount = 0.0;
    /** 판매자ID */
    protected string $seller_id = "";
    /** 배송비 */
    protected float $shipping_fee = 0.0;
    /** 환불 */
    protected float $refund = 0.0;
    /** 환불금액 */
    protected float $refund_payment = 0.0;
    /** 환불상태 */
    protected string $refund_status = "";
    /** 판매자 번호 */
    protected string $phone = "";
    /** 판매자 이메일 */
    protected string $email = "";
    /** 판매자 아임인플랫폼 */
    protected string $im_in_platform = "";
    /** 판매자 이름 */
    protected string $name = "";
    /** 판매자 핸드폰 */
    protected string $mobile = "";
    /** 판매자 회사이름 */
    protected string $company_name = "";
    /** 쿠폰수수료 */
    protected float $coupon_fee = 0.0;
    /** 수신자 전체 이름 */
    protected string $to_full_name = "";
    /** 수신자 코드 */
    protected string $to_division_code = "";
    /** 수신자 핸드폰 */
    protected string $to_mobile = "";
    /** 수신자 우편번호 */
    protected string $to_post = "";
    /** 수신자 건물번호 */
    protected string $to_town_code = "";
    /** 수신자 지역 */
    protected string $to_area = "";
    /** 주문종료 추가사유 */
    protected string $close_remark = "";
    /** 거래 유형 */
    protected string $trade_type = "";
    /** 거래ID */
    protected string $id_of_str = "";
    /** 일회성 결제 여부 */
    protected bool $step_pay_all = false;
    /** 생성시간 */
    protected ?string $create_time = null;
    /** 비즈니스 유형 */
    protected string $business_type = "";
    /** 거래 유형 설명 */
    protected string $trade_type_desc = "";
    /** 결제 채널 */
    protected string $pay_channel_list = "";
    /** 거래 유형 코드 */
    protected string $trade_type_code = "";
    /** 결제 제한 시간 */
    protected string $pay_timeout = "";
    /** 지불 시간 초과 유형 */
    protected string $pay_timeout_type = "";
    /** 결제 채널 코드 */
    protected string $pay_channel_code_list = "";

    public function bind(mixed $data): void
    {
        $this->order_id              = $data["orderId"];
        $this->offer_id              = $data["offerId"];
        $this->channel               = $data["channel"];
        $this->status                = $data["status"] ?? "";
        $this->all_delivered_time    = $data["allDeliveredTime"] ?? null;
        $this->pay_time              = $data["payTime"] ?? null;
        $this->discount              = $data["discount"] ?? 0.0;
        $this->sum_product_payment   = $data["sumProductPayment"] ?? 0.0;
        $this->modify_time           = $data["modifyTime"] ?? null;
        $this->close_reason          = $data["closeReason"] ?? "";
        $this->complete_time         = $data["completeTime"] ?? null;
        $this->close_operate_type    = $data["closeOperateType"] ?? "";
        $this->total_amount          = $data["totalAmount"] ?? 0.0;
        $this->seller_id             = $data["sellerID"] ?? "";
        $this->shipping_fee          = $data["shippingFee"] ?? 0.0;
        $this->refund                = $data["refund"] ?? 0.0;
        $this->refund_payment        = $data["refundPayment"] ?? 0.0;
        $this->refund_status         = $data["refundStatus"] ?? 0.0;
        $this->phone                 = $data["sellerContact"]["phone"] ?? "";
        $this->email                 = $data["sellerContact"]["email"] ?? "";
        $this->im_in_platform        = $data["sellerContact"]["imInPlatform"] ?? "";
        $this->name                  = $data["sellerContact"]["name"] ?? "";
        $this->mobile                = $data["sellerContact"]["mobile"] ?? "";
        $this->company_name          = $data["sellerContact"]["companyName"] ?? "";
        $this->coupon_fee            = $data["couponFee"] ?? 0.0;
        $this->to_full_name          = $data["receiverInfo"]["toFullName"] ?? "";
        $this->to_division_code      = $data["receiverInfo"]["toDivisionCode"] ?? "";
        $this->to_mobile             = $data["receiverInfo"]["toMobile"] ?? "";
        $this->to_post               = $data["receiverInfo"]["toPost"] ?? "";
        $this->to_town_code          = $data["receiverInfo"]["toTownCode"] ?? "";
        $this->to_area               = $data["receiverInfo"]["toArea"] ?? "";
        $this->close_remark          = $data["closeRemark"] ?? "";
        $this->trade_type            = $data["tradeType"] ?? "";
        $this->id_of_str             = $data["idOfStr"] ?? "";
        $this->step_pay_all          = $data["stepPayAll"] ?? false;
        $this->create_time           = $data["createTime"] ?? null;
        $this->business_type         = $data["businessType"] ?? "";
        $this->trade_type_desc       = $data["tradeTypeDesc"] ?? "";
        $this->pay_channel_list      = $data["payChannelList"][0] ?? "";
        $this->trade_type_code       = $data["tradeTypeCode"] ?? "";
        $this->pay_timeout           = $data["payTimeout"] ?? 0;
        $this->pay_timeout_type      = $data["payTimeoutType"] ?? 0;
        $this->pay_channel_code_list = $data["payChannelCodeList"][0] ?? "";

        $this->__changeKrUTCTime();
    }

    public function __changeKrUTCTime()
    {
        if ($this->all_delivered_time) {
            $this->all_delivered_time = Carbon::createFromFormat('YmdHisvO', $this->all_delivered_time)->setTimezone(config('app.timezone'));
        }
        if ($this->pay_time) {
            $this->pay_time = Carbon::createFromFormat('YmdHisvO', $this->pay_time)->setTimezone(config('app.timezone'));
        }
        if ($this->modify_time) {
            $this->modify_time = Carbon::createFromFormat('YmdHisvO', $this->modify_time)->setTimezone(config('app.timezone'));
        }
        if ($this->complete_time) {
            $this->complete_time = Carbon::createFromFormat('YmdHisvO', $this->complete_time)->setTimezone(config('app.timezone'));
        }
        if ($this->create_time) {
            $this->create_time = Carbon::createFromFormat('YmdHisvO', $this->create_time)->setTimezone(config('app.timezone'));
        }
    }
}