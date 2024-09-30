<?php

namespace App\Constants;

class WmsConstant
{
    public const USER_ID = "korea";

    /** hscode 검색종류 */
    public const HSCODE_SEARCH_TYPE_KO     = "ko";
    public const HSCODE_SEARCH_TYPE_EN     = "en";
    public const HSCODE_SEARCH_TYPE_HSCODE = "hscode";
    public const HSCODE_SEARCH_TYPE        = [
        self::HSCODE_SEARCH_TYPE_KO     => "한글명",
        self::HSCODE_SEARCH_TYPE_EN     => "영문명",
        self::HSCODE_SEARCH_TYPE_HSCODE => "HS code",
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
}
