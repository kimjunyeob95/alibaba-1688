<?php

namespace App\Constants;


class BonaeraConstant
{
    /** 입고상태 */
    public const WAREHOUSE_STATUS_PENDING  = "1001";
    public const WAREHOUSE_STATUS_RECEIVED = "1003";
    public const WAREHOUSE_STATUS_DISPOSED = "1000";
    public const WAREHOUSE_STATUS          = [
        self::WAREHOUSE_STATUS_PENDING  => "입고대기",
        self::WAREHOUSE_STATUS_RECEIVED => "입고완료",
        self::WAREHOUSE_STATUS_DISPOSED => "폐기",
    ];

    /** 재고상태 */
    public const LACK_STATUS_Y = "Y";
    public const LACK_STATUS_N = "N";
    public const LACK_STATUS   = [
        self::LACK_STATUS_Y => "재고있음",
        self::LACK_STATUS_N => "재고소진",
    ];

    /** 그룹 신청서 상태 */
    public const GROUP_STATUS_301      = "301";
    public const GROUP_STATUS_302      = "302";
    public const GROUP_STATUS_303      = "303";
    public const GROUP_STATUS_304      = "304";
    public const GROUP_STATUS_305      = "305";
    public const GROUP_STATUS_306      = "306";
    public const GROUP_STATUS_307      = "307";
    public const GROUP_STATUS_301_PEKI = "301_peki";
    public const GROUP_STATUS_302_PEKI = "302_peki";
    public const GROUP_STATUS_303_PEKI = "303_peki";
    public const GROUP_STATUS_304_PEKI = "304_peki";
    public const GROUP_STATUS_305_PEKI = "305_peki";
    public const GROUP_STATUS_306_PEKI = "306_peki";
    public const GROUP_STATUS_307_PEKI = "307_peki";
    public const GROUP_STATUS_300      = "300";
    public const GROUP_STATUS          = [
        self::GROUP_STATUS_301      => "입고대기",
        self::GROUP_STATUS_302      => "입고완료",
        self::GROUP_STATUS_303      => "무게측정",
        self::GROUP_STATUS_304      => "결제대기",
        self::GROUP_STATUS_305      => "결제확인중",
        self::GROUP_STATUS_306      => "출고준비",
        self::GROUP_STATUS_307      => "출고완료",
        self::GROUP_STATUS_300      => "폐기",
        self::GROUP_STATUS_301_PEKI => "폐기<br>(입고대기)",
        self::GROUP_STATUS_302_PEKI => "폐기<br>(입고완료)",
        self::GROUP_STATUS_303_PEKI => "폐기<br>(무게측정)",
        self::GROUP_STATUS_304_PEKI => "폐기<br>(결제대기)",
        self::GROUP_STATUS_305_PEKI => "폐기<br>(결제확인중)",
        self::GROUP_STATUS_306_PEKI => "폐기<br>(출고준비)",
        self::GROUP_STATUS_307_PEKI => "폐기<br>(출고완료)",
    ];
    public const OUT_PUBLIC_STATUS = [
        self::GROUP_STATUS_301 => self::GROUP_STATUS[self::GROUP_STATUS_301],
        self::GROUP_STATUS_302 => self::GROUP_STATUS[self::GROUP_STATUS_302],
        self::GROUP_STATUS_303 => self::GROUP_STATUS[self::GROUP_STATUS_303],
        self::GROUP_STATUS_304 => self::GROUP_STATUS[self::GROUP_STATUS_304],
        self::GROUP_STATUS_305 => self::GROUP_STATUS[self::GROUP_STATUS_305],
        self::GROUP_STATUS_306 => self::GROUP_STATUS[self::GROUP_STATUS_306],
        self::GROUP_STATUS_307 => self::GROUP_STATUS[self::GROUP_STATUS_307],
        self::GROUP_STATUS_300 => self::GROUP_STATUS[self::GROUP_STATUS_300],
    ];
    public const OUT_PEKI_STATUS = [
        self::GROUP_STATUS_300,
        self::GROUP_STATUS_301_PEKI,
        self::GROUP_STATUS_302_PEKI,
        self::GROUP_STATUS_303_PEKI,
        self::GROUP_STATUS_304_PEKI,
        self::GROUP_STATUS_305_PEKI,
        self::GROUP_STATUS_306_PEKI,
        self::GROUP_STATUS_307_PEKI,
    ];

    /** 사진 번호 */
    public const IMG_NUMBER_1  = 1;
    public const IMG_NUMBER_2  = 2;
    public const IMG_NUMBER_3  = 3;
    public const IMG_NUMBER_4  = 4;
    public const IMG_NUMBER_5  = 5;
    public const IMG_NUMBER_6  = 6;
    public const IMG_NUMBER_7  = 7;
    public const IMG_NUMBER_8  = 8;
    public const IMG_NUMBER_9  = 9;
    public const IMG_NUMBER_10 = 10;

    /** 배송비 결제 */
    public const DELIVERY_PAY_Y = "Y";
    public const DELIVERY_PAY_N = "N";

    /** 운송방법 */
    public const CTR_NUM_1 = "1";
    public const CTR_NUM_2 = "2";
    public const CTR_NUM   = [
        self::CTR_NUM_1 => "항공",
        self::CTR_NUM_2 => "해운",
    ];
}
