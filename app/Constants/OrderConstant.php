<?php

namespace App\Constants;


class OrderConstant
{
    /** 주문상태 */
    public const STATUS_WAITBUYERPAY     = "waitbuyerpay";
    public const STATUS_WAITSELLERSEND   = "waitsellersend";
    public const STATUS_WAITBUYERRECEIVE = "waitbuyerreceive";
    public const STATUS_CONFIRM_GOODS    = "confirm_goods";
    public const STATUS_SUCCESS          = "success";
    public const STATUS_CANCEL           = "cancel";
    public const STATUS_TERMINATED       = "terminated";
    public const STATUS                  = [
        self::STATUS_CANCEL           => "주문취소",
        self::STATUS_WAITBUYERPAY     => "결제대기",
        self::STATUS_WAITSELLERSEND   => "결제완료",
        self::STATUS_WAITBUYERRECEIVE => "발송완료",
        self::STATUS_CONFIRM_GOODS    => "배송완료",
        self::STATUS_SUCCESS          => "거래완료",
        self::STATUS_TERMINATED       => "거래종료"
    ];
    public const STATUS_FILTER = [
        self::STATUS_SUCCESS          => "거래완료",
        self::STATUS_CANCEL           => "주문취소",
        self::STATUS_WAITBUYERPAY     => "결제대기",
        self::STATUS_WAITSELLERSEND   => "결제완료",
        self::STATUS_WAITBUYERRECEIVE => "발송완료",
    ];

    /** 주문 취소 사유 */
    public const CANCEL_REASON_BUYER_CANCEL            = "buyerCancel";
    public const CANCEL_REASON_BUYER_SELLER_GOODS_LACK = "sellerGoodsLack";
    public const CANCEL_REASON_BUYER_OTHER             = "other";
    public const CANCEL_REASON                         = [
        self::CANCEL_REASON_BUYER_CANCEL            => "구매자 취소",
        self::CANCEL_REASON_BUYER_SELLER_GOODS_LACK => "재고부족",
        self::CANCEL_REASON_BUYER_OTHER             => "기타",
    ];

    /** 거래 종료 사유 */
    public const CLOSE_REASON_TRADE_BY_SELLER  = "CLOSE_TRADE_BY_SELLER";
    public const CLOSE_REASON_TRADE_BY_BUYER   = "CLOSE_TRADE_BY_BUYER";
    public const CLOSE_REASON_TRADE_BY_BOPS    = "CLOSE_TRADE_BY_BOPS";
    public const CLOSE_REASON_TRADE_BY_SYSTEM  = "CLOSE_TRADE_BY_SYSTEM";
    public const CLOSE_REASON_TRADE_BY_CREADIT = "CLOSE_TRADE_BY_CREADIT";
    public const CLOSE_REASON                  = [
        self::CLOSE_REASON_TRADE_BY_SELLER  => "판매자종료",
        self::CLOSE_REASON_TRADE_BY_BUYER   => "구매자종료",
        self::CLOSE_REASON_TRADE_BY_BOPS    => "백그라운드에서 종료",
        self::CLOSE_REASON_TRADE_BY_SYSTEM  => "시스템(시간초과)",
        self::CLOSE_REASON_TRADE_BY_CREADIT => "기타",
    ];

    /** 환불 상태 */
    public const REFUND_STATUS_WAITSELLERAGREE   = "waitselleragree";
    public const REFUND_STATUS_WAITBUYERMODIFY   = "waitbuyermodify";
    public const REFUND_STATUS_WAITBUYERSEND     = "waitbuyersend";
    public const REFUND_STATUS_WAITSELLERRECEIVE = "waitsellerreceive";
    public const REFUND_STATUS_REFUNDSUCCESS     = "refundsuccess";
    public const REFUND_STATUS_REFUNDCLOSE       = "refundclose";
    public const REFUND_STATUS                   = [
        self::REFUND_STATUS_WAITSELLERAGREE   => "환불요청",
        self::REFUND_STATUS_WAITBUYERMODIFY   => "환불 확인 중",
        self::REFUND_STATUS_WAITBUYERSEND     => "반품 상품 발송 전",
        self::REFUND_STATUS_WAITSELLERRECEIVE => "반품 상품 수령 전",
        self::REFUND_STATUS_REFUNDSUCCESS     => "환불완료",
        self::REFUND_STATUS_REFUNDCLOSE       => "환불실패",
    ];

    /** 지불 상태 */
    public const PAY_STATUS_WAIT_PAY     = "WAIT_PAY";
    public const PAY_STATUS_PAYER_PAID   = "PAYER_PAID";
    public const PAY_STATUS_PART_SUCCESS = "PART_SUCCESS";
    public const PAY_STATUS_PAY_SUCCESS  = "PAY_SUCCESS";
    public const PAY_STATUS_CLOSED       = "CLOSED";
    public const PAY_STATUS_CANCELED     = "CANCELED";
    public const PAY_STATUS_SUCCESS      = "SUCCESS";
    public const PAY_STATUS_FAIL         = "FAIL";
    public const PAY_STATUS_1            = "1";
    public const PAY_STATUS_2            = "2";
    public const PAY_STATUS_6            = "6";
    public const PAY_STATUS_7            = "7";
    public const PAY_STATUS_9            = "9";
    public const PAY_STATUS_12           = "12";
    public const PAY_STATUS              = [
        self::PAY_STATUS_WAIT_PAY     => "미결제",
        self::PAY_STATUS_PAYER_PAID   => "결제완료",
        self::PAY_STATUS_PART_SUCCESS => "부분결제성공",
        self::PAY_STATUS_PAY_SUCCESS  => "결제성공",
        self::PAY_STATUS_CLOSED       => "위험관리종료",
        self::PAY_STATUS_CANCELED     => "결제취소",
        self::PAY_STATUS_SUCCESS      => "성공",
        self::PAY_STATUS_FAIL         => "실패",
        self::PAY_STATUS_1            => "미지급",
        self::PAY_STATUS_2            => "지불",
        self::PAY_STATUS_6            => "판매자가 돈을 받았고 지불이 완료됨",
        self::PAY_STATUS_7            => "외부 지불 주문이 생성되지 않음",
        self::PAY_STATUS_9            => "결제 진행 중",
        self::PAY_STATUS_12           => "결제 예정, 수신 대기 중",
    ];

    /** 거래유형 */
    public const TRADE_TYPE_1     = "1";
    public const TRADE_TYPE_2     = "2";
    public const TRADE_TYPE_3     = "3";
    public const TRADE_TYPE_4     = "4";
    public const TRADE_TYPE_5     = "5";
    public const TRADE_TYPE_6     = "6";
    public const TRADE_TYPE_7     = "7";
    public const TRADE_TYPE_8     = "8";
    public const TRADE_TYPE_9     = "9";
    public const TRADE_TYPE_10    = "10";
    public const TRADE_TYPE_50060 = "50060";
    public const TRADE_TYPE       = [
        self::TRADE_TYPE_1     => "보증거래",
        self::TRADE_TYPE_2     => "선입금거래",
        self::TRADE_TYPE_3     => "ETC 해외취득거래",
        self::TRADE_TYPE_4     => "즉시지불거래",
        self::TRADE_TYPE_5     => "보증금거래",
        self::TRADE_TYPE_6     => "일원화된 거래프로세스",
        self::TRADE_TYPE_7     => "단계별지급",
        self::TRADE_TYPE_8     => "현금결제거래",
        self::TRADE_TYPE_9     => "신용카드결제거래",
        self::TRADE_TYPE_10    => "기간지불 거래",
        self::TRADE_TYPE_50060 => "거래 4.0",
    ];

    /** 비즈니스 유형 */
    public const BUSINESS_TYPE_CN           = "cn";
    public const BUSINESS_TYPE_WS           = "ws";
    public const BUSINESS_TYPE_YP           = "yp";
    public const BUSINESS_TYPE_YF           = "yf";
    public const BUSINESS_TYPE_FS           = "fs";
    public const BUSINESS_TYPE_CZ           = "cz";
    public const BUSINESS_TYPE_AG           = "ag";
    public const BUSINESS_TYPE_HP           = "hp";
    public const BUSINESS_TYPE_SUPPLY       = "supply";
    public const BUSINESS_TYPE_FACTORY      = "factory";
    public const BUSINESS_TYPE_QUICK        = "quick";
    public const BUSINESS_TYPE_XIANGPIN     = "xiangpin";
    public const BUSINESS_TYPE_F2F          = "f2f";
    public const BUSINESS_TYPE_CYFW         = "cyfw";
    public const BUSINESS_TYPE_SP           = "sp";
    public const BUSINESS_TYPE_WG           = "wg";
    public const BUSINESS_TYPE_LST          = "lst";
    public const BUSINESS_TYPE_CB           = "cb";
    public const BUSINESS_TYPE_DISTRIBUTION = "distribution";
    public const BUSINESS_TYPE_CAB          = "cab";
    public const BUSINESS_TYPE_MANUFACT     = "manufact";
    public const BUSINESS_TYPE = [
        self::BUSINESS_TYPE_CN           => "일반 주문 유형",
        self::BUSINESS_TYPE_WS           => "대규모 도매 주문 유형",
        self::BUSINESS_TYPE_YP           => "일반 샘플 주문 유형",
        self::BUSINESS_TYPE_YF           => "1센트 샘플 주문형",
        self::BUSINESS_TYPE_FS           => "역배치(기간 한정 할인) 주문 유형",
        self::BUSINESS_TYPE_CZ           => "맞춤 주문 유형 처리",
        self::BUSINESS_TYPE_AG           => "계약 구매 주문 유형",
        self::BUSINESS_TYPE_HP           => "파트너 주문 유형",
        self::BUSINESS_TYPE_SUPPLY       => "공급 및 판매 주문 유형",
        self::BUSINESS_TYPE_FACTORY      => "타오바오 공장주문",
        self::BUSINESS_TYPE_QUICK        => "빠른 주문",
        self::BUSINESS_TYPE_XIANGPIN     => "공유 주문",
        self::BUSINESS_TYPE_F2F          => "직접 결제",
        self::BUSINESS_TYPE_CYFW         => "샘플입금",
        self::BUSINESS_TYPE_SP           => "위탁주문",
        self::BUSINESS_TYPE_WG           => "마이크로 공급 주문",
        self::BUSINESS_TYPE_LST          => "소매 링크",
        self::BUSINESS_TYPE_CB           => "국경 간 주문",
        self::BUSINESS_TYPE_DISTRIBUTION => "유통",
        self::BUSINESS_TYPE_CAB          => "차이위안바오",
        self::BUSINESS_TYPE_MANUFACT     => "맞춤 처리",
    ];

    /** 결제시간 초과 유형  */
    public const PAY_TIMEOUT_TYPE_0 = 0;
    public const PAY_TIMEOUT_TYPE_1 = 1;
    public const PAY_TIMEOUT_TYPE   = [
        self::PAY_TIMEOUT_TYPE_0 => "고정 길이",
        self::PAY_TIMEOUT_TYPE_1 => "고정 시간",
    ];

    /** 상품 상태 */
    public const PRD_STATUS_WAITBUYERPAY     = "waitbuyerpay";
    public const PRD_STATUS_WAITSELLERSEND   = "waitsellersend";
    public const PRD_STATUS_WAITBUYERRECEIVE = "waitbuyerreceive";
    public const PRD_STATUS_CONFIRM_GOODS    = "confirm_goods";
    public const PRD_STATUS_SUCCESS          = "success";
    public const PRD_STATUS_CANCLE           = "cancle";
    public const PRD_STATUS_TERMINATED       = "terminated";
    public const PRD_STATUS                  = [
        self::PRD_STATUS_WAITBUYERPAY     => "결제대기(구매자가 결제대기)",
        self::PRD_STATUS_WAITSELLERSEND   => "결제완료(판매자 발송 대기)",
        self::PRD_STATUS_WAITBUYERRECEIVE => "출고 (구매사 수령대기)",
        self::PRD_STATUS_CONFIRM_GOODS    => "배송완료(수령확인)",
        self::PRD_STATUS_SUCCESS          => "거래완료",
        self::PRD_STATUS_CANCLE           => "거래취소",
        self::PRD_STATUS_TERMINATED       => "거래종료 ",
    ];

    /** 물류상태 */
    public const LOGISTICS_STATUS_1 = 1;
    public const LOGISTICS_STATUS_2 = 2;
    public const LOGISTICS_STATUS_3 = 3;
    public const LOGISTICS_STATUS_4 = 4;
    public const LOGISTICS_STATUS_5 = 5;
    public const LOGISTICS_STATUS_8 = 8;
    public const LOGISTICS_STATUS   = [
        self::LOGISTICS_STATUS_1 => "배송전",
        self::LOGISTICS_STATUS_2 => "배송중",
        self::LOGISTICS_STATUS_3 => "배송완료",
        self::LOGISTICS_STATUS_4 => "반송",
        self::LOGISTICS_STATUS_5 => "부분 배송",
        self::LOGISTICS_STATUS_8 => "접수전",
    ];

    /** 결제 방법 */
    public const PAY_ALIPAY       = "alipay";
    public const PAY_CROSS_BORDER = "crossBorder";

}
