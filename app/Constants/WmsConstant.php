<?php

namespace App\Constants;

class WmsConstant
{
    public const COMPANY_BONAERA = "bonaera";

    /** hscode 검색종류 */
    public const HSCODE_SEARCH_TYPE_KO     = "ko";
    public const HSCODE_SEARCH_TYPE_EN     = "en";
    public const HSCODE_SEARCH_TYPE_HSCODE = "hscode";
    public const HSCODE_SEARCH_TYPE_SH_NO  = "sh_no";
    public const HSCODE_SEARCH_TYPE        = [
        self::HSCODE_SEARCH_TYPE_KO     => "한글명",
        self::HSCODE_SEARCH_TYPE_EN     => "영문명",
        self::HSCODE_SEARCH_TYPE_HSCODE => "HS code",
        self::HSCODE_SEARCH_TYPE_SH_NO  => "품목코드",
    ];

    /** 입고관리 검색종류 */
    public const IN_SEARCH_TYPE_STOCK_NO         = "stock_no";
    public const IN_SEARCH_TYPE_ORDER_ID         = "order_id";
    public const IN_SEARCH_TYPE_CHANNEL_ORDER_ID = "channel_order_id";
    public const IN_SEARCH_TYPE                  = [
        self::IN_SEARCH_TYPE_STOCK_NO         => "입고번호",
        self::IN_SEARCH_TYPE_ORDER_ID         => "W 주문번호",
        self::IN_SEARCH_TYPE_CHANNEL_ORDER_ID => "채널 주문번호",
    ];

    /** 입고 요청 실패 검색종류 */
    public const IN_FAIL_SEARCH_TYPE_ORDER_ID         = "order_id";
    public const IN_FAIL_SEARCH_TYPE_CHANNEL_ORDER_ID = "channel_order_id";
    public const IN_FAIL_SEARCH_TYPE_OFFER_ID         = "offer_id";
    public const IN_FAIL_SEARCH_TYPE                  = [
        self::IN_FAIL_SEARCH_TYPE_ORDER_ID         => "W 주문번호",
        self::IN_FAIL_SEARCH_TYPE_CHANNEL_ORDER_ID => "채널 주문번호",
        self::IN_FAIL_SEARCH_TYPE_OFFER_ID         => "제품ID",
    ];

    /** 출고 신청관리 상태 */
    public const OUT_SIGN_STATUS_SUCCESS = "success";
    public const OUT_SIGN_STATUS_FAIL    = "fail";
    public const OUT_SIGN_STATUS_WAIT    = "wait";
    public const OUT_SIGN_STATUS         = [
        self::OUT_SIGN_STATUS_SUCCESS => "완료",
        self::OUT_SIGN_STATUS_FAIL    => "실패",
        self::OUT_SIGN_STATUS_WAIT    => "대기",
    ];

    /** 출고관리 신청관리 검색종류 */
    public const OUT_SIGN_SEARCH_TYPE_ORDER_ID         = "order_id";
    public const OUT_SIGN_SEARCH_TYPE_CHANNEL_ORDER_ID = "channel_order_id";
    public const OUT_SIGN_SEARCH_TYPE_STOCK_NO         = "stock_no";
    public const OUT_SIGN_SEARCH_TYPE_SH_NO            = "sh_no";
    public const OUT_SIGN_SEARCH_TYPE_GROUP_NO         = "group_no";
    public const OUT_SIGN_SEARCH_TYPE                  = [
        self::OUT_SIGN_SEARCH_TYPE_ORDER_ID         => "W 주문번호",
        self::OUT_SIGN_SEARCH_TYPE_CHANNEL_ORDER_ID => "채널 주문번호",
        self::OUT_SIGN_SEARCH_TYPE_STOCK_NO         => "입고번호",
        self::OUT_SIGN_SEARCH_TYPE_SH_NO            => "출고번호",
        self::OUT_SIGN_SEARCH_TYPE_GROUP_NO         => "배송번호",
    ];

    /** 출고 리스트 검색종류 */
    public const OUT_SEARCH_TYPE_GROUP_NO         = "group_no";
    public const OUT_SEARCH_TYPE_SH_NO            = "sh_no";
    public const OUT_SEARCH_TYPE_CHANNEL_ORDER_ID = "channel_order_id";
    public const OUT_SEARCH_TYPE_INVOICE          = "invoice";
    public const OUT_SEARCH_TYPE                  = [
        self::OUT_SEARCH_TYPE_GROUP_NO         => "배송번호",
        self::OUT_SEARCH_TYPE_SH_NO            => "출고번호",
        self::OUT_SEARCH_TYPE_CHANNEL_ORDER_ID => "채널 주문번호",
        self::OUT_SEARCH_TYPE_INVOICE          => "운송장번호",
    ];

    /** 출고리스트 검색종류 */
    public const OUT_LIST_STATUS_302_303 = "302_303";
    public const OUT_LIST_STATUS_302_304 = "302_304";
    public const OUT_LIST_STATUS_302_305 = "302_305";
    public const OUT_LIST_STATUS_302_306 = "302_306";
    public const OUT_LIST_STATUS_302_307 = "302_307";
    public const OUT_LIST_STATUS_300     = "300";
    public const OUT_LIST_STATUS         = [
        self::OUT_LIST_STATUS_302_303 => "무게측정",
        self::OUT_LIST_STATUS_302_304 => "결제대기",
        self::OUT_LIST_STATUS_302_305 => "결제확인중",
        self::OUT_LIST_STATUS_302_306 => "출고준비",
        self::OUT_LIST_STATUS_302_307 => "출고완료",
        self::OUT_LIST_STATUS_300     => "폐기",
    ];

    /** WMS PUB/SUB 코드타입 */
    public const WMS_CODE_TYPE_IT000 = "IT000";
    public const WMS_CODE_TYPE_IT001 = "IT001";
    public const WMS_CODE_TYPE_IT002 = "IT002";
    public const WMS_CODE_TYPE_SH000 = "SH000";
    public const WMS_CODE_TYPE_SH001 = "SH001";
    public const WMS_CODE_TYPE_SH002 = "SH002";
    public const WMS_CODE_TYPE_GR001 = "GR001";
    public const WMS_CODE_TYPE_GR002 = "GR002";
    public const WMS_CODE_TYPE_GR003 = "GR003";
    public const WMS_CODE_TYPE       = [
        self::WMS_CODE_TYPE_IT000 => "입고 신청 완료",
        self::WMS_CODE_TYPE_IT001 => "입고 상태의 변경",
        self::WMS_CODE_TYPE_IT002 => "입고 정보 변경",
        self::WMS_CODE_TYPE_SH000 => "출고 신청 완료",
        self::WMS_CODE_TYPE_SH001 => "출고 상태의 변경",
        self::WMS_CODE_TYPE_SH002 => "출고 정보의 변경",
        self::WMS_CODE_TYPE_GR001 => "배송 상태의 변경",
        self::WMS_CODE_TYPE_GR002 => "배송 정보의 변경",
        self::WMS_CODE_TYPE_GR003 => "묶음 배송변경",
    ];
    public const WMS_CODE_TYPE_LIST = [
        self::WMS_CODE_TYPE_IT001,
        self::WMS_CODE_TYPE_IT002,
        self::WMS_CODE_TYPE_SH001,
        self::WMS_CODE_TYPE_SH002,
        self::WMS_CODE_TYPE_GR001,
        self::WMS_CODE_TYPE_GR002,
        self::WMS_CODE_TYPE_GR003,
    ];

    /** 부가서비스 종류 */
    public const OUT_EXTRA_NAME          = "출고";
    public const OUT_DELIVERY_EXTRA_NAME = "배송";
}
