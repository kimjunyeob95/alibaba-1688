<?php

namespace App\Constants;


class OrderConstant
{
    // 주문 상태
    public const wait_pay         = "waitbuyerpay";
    public const wait_delivery    = "waitellerend";
    public const wait_receive     = "waitbuyerreceive";
    public const receive_done     = "confirm_goods";
    public const order_success    = "success";
    public const order_cancel     = "cancel";
    public const order_terminated = "terminated";

    public const order_status_list = [
        self::wait_pay         => "결제 대기",
        self::wait_delivery    => "발송 대기",
        self::wait_receive     => "배송중",
        self::receive_done     => "배송완료",
        self::order_success    => "주문 성공",
        self::order_cancel     => "주문 취소",
        self::order_terminated => "거래종료(기타상태)"
    ];
}
