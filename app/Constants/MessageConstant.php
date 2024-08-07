<?php

namespace App\Constants;


class MessageConstant
{
    /** 1688 메세지 리스트 */
    public const ORDER_BUYER_VIEW_BUYER_MAKE                  = "ORDER_BUYER_VIEW_BUYER_MAKE";
    public const ORDER_BUYER_VIEW_ORDER_PRICE_MODIFY          = "ORDER_BUYER_VIEW_ORDER_PRICE_MODIFY";
    public const ORDER_BUYER_VIEW_ORDER_SUCCESS               = "ORDER_BUYER_VIEW_ORDER_SUCCESS";
    public const ORDER_BUYER_VIEW_ORDER_PAY                   = "ORDER_BUYER_VIEW_ORDER_PAY";
    public const ORDER_BUYER_VIEW_ORDER_STEP_PAY              = "ORDER_BUYER_VIEW_ORDER_STEP_PAY";
    public const ORDER_BATCH_PAY                              = "ORDER_BATCH_PAY";
    public const ORDER_BUYER_VIEW_ANNOUNCE_SENDGOODS          = "ORDER_BUYER_VIEW_ANNOUNCE_SENDGOODS";
    public const ORDER_BUYER_VIEW_PART_PART_SENDGOODS         = "ORDER_BUYER_VIEW_PART_PART_SENDGOODS";
    public const ORDER_BUYER_VIEW_ORDER_COMFIRM_RECEIVEGOODS  = "ORDER_BUYER_VIEW_ORDER_COMFIRM_RECEIVEGOODS";
    public const ORDER_BUYER_VIEW_ORDER_BUYER_CLOSE           = "ORDER_BUYER_VIEW_ORDER_BUYER_CLOSE";
    public const ORDER_BUYER_VIEW_ORDER_SELLER_CLOSE          = "ORDER_BUYER_VIEW_ORDER_SELLER_CLOSE";
    public const ORDER_BUYER_VIEW_ORDER_BOPS_CLOSE            = "ORDER_BUYER_VIEW_ORDER_BOPS_CLOSE";
    public const ORDER_BUYER_VIEW_ORDER_BUYER_REFUND_IN_SALES = "ORDER_BUYER_VIEW_ORDER_BUYER_REFUND_IN_SALES";
    public const ORDER_BUYER_VIEW_ORDER_REFUND_AFTER_SALES    = "ORDER_BUYER_VIEW_ORDER_REFUND_AFTER_SALES";
    public const LOGISTICS_BUYER_VIEW_TRACE                   = "LOGISTICS_BUYER_VIEW_TRACE";
    public const LOGISTICS_MAIL_NO_CHANGE                     = "LOGISTICS_MAIL_NO_CHANGE";

    public const OM001 = "OM001";
    public const OM002 = "OM002";
    public const OM003 = "OM003";
    public const OP001 = "OP001";
    public const OP002 = "OP002";
    public const OP003 = "OP003";
    public const OS001 = "OS001";
    public const OS002 = "OS002";
    public const OS003 = "OS003";
    public const OC001 = "OC001";
    public const OC002 = "OC002";
    public const OC003 = "OC003";
    public const OR001 = "OR001";
    public const OR002 = "OR002";
    public const OT001 = "OT001";
    public const OT002 = "OT002";

    public const MESSAGE_CODE = [
        self::ORDER_BUYER_VIEW_BUYER_MAKE                  => self::OM001,
        self::ORDER_BUYER_VIEW_ORDER_PRICE_MODIFY          => self::OM002,
        self::ORDER_BUYER_VIEW_ORDER_SUCCESS               => self::OM003,
        self::ORDER_BUYER_VIEW_ORDER_PAY                   => self::OP001,
        self::ORDER_BUYER_VIEW_ORDER_STEP_PAY              => self::OP002,
        self::ORDER_BATCH_PAY                              => self::OP003,
        self::ORDER_BUYER_VIEW_ANNOUNCE_SENDGOODS          => self::OS001,
        self::ORDER_BUYER_VIEW_PART_PART_SENDGOODS         => self::OS002,
        self::ORDER_BUYER_VIEW_ORDER_COMFIRM_RECEIVEGOODS  => self::OS003,
        self::ORDER_BUYER_VIEW_ORDER_BUYER_CLOSE           => self::OC001,
        self::ORDER_BUYER_VIEW_ORDER_SELLER_CLOSE          => self::OC002,
        self::ORDER_BUYER_VIEW_ORDER_BOPS_CLOSE            => self::OC003,
        self::ORDER_BUYER_VIEW_ORDER_BUYER_REFUND_IN_SALES => self::OR001,
        self::ORDER_BUYER_VIEW_ORDER_REFUND_AFTER_SALES    => self::OR002,
        self::LOGISTICS_BUYER_VIEW_TRACE                   => self::OT001,
        self::LOGISTICS_MAIL_NO_CHANGE                     => self::OT002,
    ];

    public const MESSAGE_CODE_NAME = [
        self::OM001 => "주문 생성",
        self::OM002 => "주문 가격수정",
        self::OM003 => "거래완료",
        self::OP001 => "주문 결제완료",
        self::OP002 => "결제 단계별 결제",
        self::OP003 => "결제 일괄결제",
        self::OS001 => "배송 판매자 배송",
        self::OS002 => "배송 부분 배송",
        self::OS003 => "배송 수령확인",
        self::OC001 => "취소 구매자",
        self::OC002 => "취소 판매자",
        self::OC003 => "취소 결제시간초과",
        self::OR001 => "환불 거래완료 전",
        self::OR002 => "환불 거래완료 후",
        self::OT001 => "배송 상태의 변경",
        self::OT002 => "배송 정보의 변경",
    ];
    
}
