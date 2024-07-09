<?php

namespace App\Constants;


class OrderConstant
{
    /** 주문상태 */
    public const WAIT_PAY         = "waitbuyerpay";
    public const WAIT_DELIVERY    = "waitellerend";
    public const WAIT_RECEIVE     = "waitbuyerreceive";
    public const RECEIVE_DONE     = "confirm_goods";
    public const ORDER_SUCCESS    = "success";
    public const ORDER_CANCEL     = "cancel";
    public const ORDER_TERMINATED = "terminated";
    public const ORDER_STATUS     = [
        self::WAIT_PAY         => "결제대기(구매자 결제대기)",
        self::WAIT_DELIVERY    => "결제완료(판매자 발송 대기)",
        self::WAIT_RECEIVE     => "출고(구매자 수령대기)",
        self::RECEIVE_DONE     => "배송완료(수령확인)",
        self::ORDER_SUCCESS    => "거래완료",
        self::ORDER_CANCEL     => "거래취소",
        self::ORDER_TERMINATED => "거래종료(기타상태)"
    ];

    /** 주문 취소 사유 */
    public const CLOSE_REASON_BUYER_CANCEL            = "buyerCancel";
    public const CLOSE_REASON_BUYER_SELLER_GOODS_LACK = "sellerGoodsLack";
    public const CLOSE_REASON_BUYER_OTHER             = "other";
    public const CLOSE_REASON                         = [
        self::CLOSE_REASON_BUYER_CANCEL            => "구매자 취소",
        self::CLOSE_REASON_BUYER_SELLER_GOODS_LACK => "재고부족",
        self::CLOSE_REASON_BUYER_OTHER             => "기타",
    ];

    /** 거래 종료 사유 */
    public const CLOSE_ORDER_CLOSE_TRADE_BY_SELLER  = "CLOSE_TRADE_BY_SELLER";
    public const CLOSE_ORDER_CLOSE_TRADE_BY_BUYER   = "CLOSE_TRADE_BY_BUYER";
    public const CLOSE_ORDER_CLOSE_TRADE_BY_BOPS    = "CLOSE_TRADE_BY_BOPS";
    public const CLOSE_ORDER_CLOSE_TRADE_BY_SYSTEM  = "CLOSE_TRADE_BY_SYSTEM";
    public const CLOSE_ORDER_CLOSE_TRADE_BY_CREADIT = "CLOSE_TRADE_BY_CREADIT";
    public const CLOSE_ORDER                        = [
        self::CLOSE_ORDER_CLOSE_TRADE_BY_SELLER  => "판매자종료",
        self::CLOSE_ORDER_CLOSE_TRADE_BY_BUYER   => "구매자종료",
        self::CLOSE_ORDER_CLOSE_TRADE_BY_BOPS    => "백그라운드에서 종료",
        self::CLOSE_ORDER_CLOSE_TRADE_BY_SYSTEM  => "시스템(시간초과)",
        self::CLOSE_ORDER_CLOSE_TRADE_BY_CREADIT => "기타",
    ];

    /** 환불 사유 */
    public const REFUND_WAITSELLERAGREE   = "waitselleragree";
    public const REFUND_WAITBUYERMODIFY   = "waitbuyermodify";
    public const REFUND_WAITBUYERSEND     = "waitbuyersend";
    public const REFUND_WAITSELLERRECEIVE = "waitsellerreceive";
    public const REFUND_REFUNDSUCCESS     = "refundsuccess";
    public const REFUND_REFUNDCLOSE       = "refundclose";
    public const REFUND_REASON            = [
        self::REFUND_WAITSELLERAGREE   => "판매자 동의대기",
        self::REFUND_WAITBUYERMODIFY   => "구매자 수정대기",
        self::REFUND_WAITBUYERSEND     => "구매자 발송대기",
        self::REFUND_WAITSELLERRECEIVE => "판매자 수령대기",
        self::REFUND_REFUNDSUCCESS     => "환불완료",
        self::REFUND_REFUNDCLOSE       => "환불실패",
    ];
}
