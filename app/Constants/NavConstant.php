<?php

namespace App\Constants;


class NavConstant
{
    public const W                          = "W";
    public const PRODUCT                    = "상품";
    public const PRODUCT_COLLECT_MANAGE     = "상품 수집 관리";
    public const PRODUCT_COLLECT_ID         = "상품 ID로 수집";
    public const PRODUCT_COLLECT_BASIC      = "기본 정보로 수집";
    public const PRODUCT_COLLECT_DETAIL_URL = "상품상세 URL로 수집";
    public const PRODUCT_COLLECT_SINGLE_IMG = "상품 단일 Image로 수집";
    public const PRODUCT_COLLECT_MULTI_IMG  = "상품 멀티 Image로 수집";
    public const PRODUCT_COLLECT_HISTORY    = "상품 수집 현황";
    public const MESSAGE                    = "메세지 관리";
    public const MESSAGE_LIST               = "메세지 리스트";
    
    public const W_APP                    = "W App";
    public const PRODUCT_MANAGE           = "상품 관리";
    public const PRODUCT_ALL              = "전체 상품";
    public const PRODUCT_EXCEPT_ALL       = "판매제외 상품 리스트";
    public const ORDER_MANAGE             = "주문 관리";
    public const ORDER_W_LIST             = "W 주문 리스트";
    public const ORDER_WAPP_LIST          = "WApp 주문 리스트";
    public const CATEGORY_MANAGE          = "카테고리 관리";
    public const CATEGORY_MAPPING_MANAGE  = "맵핑 관리";
    public const CATEGORY_DELIVERY_MANAGE = "표준 중량(배송비) 관리";
    public const FORBIDDEN_MANAGE         = "금칙어 관리";
    public const FORBIDDEN_PRODUCT_MANAGE = "상품정보 관리";
    public const FORBIDDEN_NOTICE_MANAGE  = "정보고시 관리";
    public const EXCEPT_MANAGE            = "제외 관리";
    public const EXCEPT_NOTICE_MANAGE     = "정보고시 관리";
    public const ADMIN_MANAGE             = "관리자 관리";
    public const ADMIN_REGIST             = "관리자 등록";

    public const WMS               = "WMS";
    public const WMS_HSCODE_MANAGE = "HS code 관리";
    public const WMS_IN_MANAGE     = "입고관리";
    public const WMS_OUT_MANAGE    = "출고관리";

    public const CHANNEL_MANAGE            = "채널 관리";
    public const CHANNEL_PRODUCT_MANAGE    = "상품 전송 현황";
    public const EASYSELL_W                = "이지셀: 더블유";
    public const EASYSELL_DROP_HUB         = "이지셀: Drop Hub";
    public const ONCHANNEL_PUBLIC          = "온채널: 일반상품";
    public const ONCHANNEL_PRIVATE         = "온채널: 사입상품";
    public const CHANNEL_CATEGORY_MANAGE   = "카테고리 관리";
    public const EASYSELL_CATEGORY_MANAGE  = "이지셀 카테고리 맵핑";
    public const ONCHANNEL_CATEGORY_MANAGE = "온채널 카테고리 맵핑";
    public const SEND_CATEGORY_MANAGE      = "전송 카테고리 맵핑";

    public const SAI                    = "SAI";
    public const QUEUE_MANAGE           = "큐 관리";
    public const WAPP_QUEUE_MANAGE      = "WApp 큐 관리";
    public const ONCHANNEL_QUEUE_MANAGE = "온채널 큐 관리";

    public const NAV_LIST = [
        self::W => [
            self::PRODUCT => [
                self::PRODUCT_COLLECT_MANAGE => [
                    self::PRODUCT_COLLECT_ID         => "/product/queryProductDetail",
                    self::PRODUCT_COLLECT_BASIC      => "/product/keywordQuery",
                    self::PRODUCT_COLLECT_DETAIL_URL => "/product/urlQuery",
                    self::PRODUCT_COLLECT_SINGLE_IMG => "/product/imageQuery",
                    self::PRODUCT_COLLECT_MULTI_IMG  => "/product/imageMultiQuery",
                    self::PRODUCT_COLLECT_HISTORY    => "/product/collectLogs",
                ]
            ],
            self::MESSAGE => [
                self::MESSAGE_LIST => "/message/list"
            ]
        ],
        self::W_APP => [
            self::PRODUCT_MANAGE => [
                self::PRODUCT_ALL        => "/product/list",
                self::PRODUCT_EXCEPT_ALL => "/product/except/list",
            ],
            self::ORDER_MANAGE => [
                self::ORDER_WAPP_LIST => "/wapp/order/list",
                self::ORDER_W_LIST    => "/wapp/order/w/list",
            ],
            self::CATEGORY_MANAGE => [
                self::CATEGORY_MAPPING_MANAGE  => "/category",
                self::CATEGORY_DELIVERY_MANAGE => "/category/weight/list",
            ],
            self::FORBIDDEN_MANAGE => [
                self::FORBIDDEN_PRODUCT_MANAGE => "/forbiddenWord/list",
                self::FORBIDDEN_NOTICE_MANAGE  => "/forbiddenWord/notice/list"
            ],
            self::EXCEPT_MANAGE => [
                self::EXCEPT_NOTICE_MANAGE => "/except/notice/list"
            ],
            self::ADMIN_MANAGE => [
                self::ADMIN_REGIST => "/wapp/admin/regist"
            ]
        ],
        self::WMS => [
            self::WMS_HSCODE_MANAGE => "/wapp/wms/hscode",
            self::WMS_IN_MANAGE     => "/wapp/wms/in",
            // self::WMS_OUT_MANAGE    => "/wapp/wms/hscode",
        ],
        self::CHANNEL_MANAGE => [
            self::CHANNEL_PRODUCT_MANAGE => [
                self::EASYSELL_W        => "/easySell/product/list/" . EasySellConstant::TYPE_W,
                self::EASYSELL_DROP_HUB => "/easySell/product/list/" . EasySellConstant::TYPE_DROPHUB,
                self::ONCHANNEL_PUBLIC  => "/onchannel/product/list/" . OnchannelConstant::PRD_CHANNEL,
                self::ONCHANNEL_PRIVATE => "/onchannel/product/list/" . OnchannelConstant::PRD_CHANNEL_PRIVATE,
            ],
            self::CHANNEL_CATEGORY_MANAGE => [
                self::EASYSELL_CATEGORY_MANAGE  => "/easySell/category",
                self::ONCHANNEL_CATEGORY_MANAGE => "/onchannel/category",
                self::SEND_CATEGORY_MANAGE      => "/category/send/mall",
            ]
        ],
        self::SAI => [
            self::QUEUE_MANAGE => [
                self::WAPP_QUEUE_MANAGE      => "/sai/queue/wapp",
                self::ONCHANNEL_QUEUE_MANAGE => "/sai/queue/onchannel",
            ]
        ],
    ];

    public const NAV_ICON = [
        self::PRODUCT                 => "/vendors/@coreui/icons/svg/free.svg#cil-basket",
        self::PRODUCT_COLLECT_MANAGE  => "/vendors/@coreui/icons/svg/free.svg#cil-basket",
        self::MESSAGE                 => "/vendors/@coreui/icons/svg/free.svg#cil-chat-bubble",
        self::MESSAGE_LIST            => "/vendors/@coreui/icons/svg/free.svg#cil-list",
        self::PRODUCT_MANAGE          => "/vendors/@coreui/icons/svg/free.svg#cil-basket",
        self::ORDER_MANAGE            => "/vendors/@coreui/icons/svg/free.svg#cil-cart",
        self::CATEGORY_MANAGE         => "/vendors/@coreui/icons/svg/free.svg#cil-list-rich",
        self::FORBIDDEN_MANAGE        => "/vendors/@coreui/icons/svg/free.svg#cil-language",
        self::EXCEPT_MANAGE           => "/vendors/@coreui/icons/svg/free.svg#cil-ban",
        self::ADMIN_MANAGE            => "/vendors/@coreui/icons/svg/free.svg#cil-user",
        self::CHANNEL_PRODUCT_MANAGE  => "/vendors/@coreui/icons/svg/free.svg#cil-usb",
        self::CHANNEL_CATEGORY_MANAGE => "/vendors/@coreui/icons/svg/free.svg#cil-folder-open",
        self::QUEUE_MANAGE            => "/vendors/@coreui/icons/svg/free.svg#cil-list-numbered",
        self::WMS_IN_MANAGE           => "/vendors/@coreui/icons/svg/free.svg#cil-media-step-backward",
        self::WMS_OUT_MANAGE          => "/vendors/@coreui/icons/svg/free.svg#cil-media-step-forward",
    ];
}
