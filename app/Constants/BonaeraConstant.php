<?php

namespace App\Constants;


class BonaeraConstant
{
    public const USER_ID = "korea";

    /** 입고상태 */
    public const WAREHOUSE_STATUS_PENDING  = "PENDING";
    public const WAREHOUSE_STATUS_RECEIVED = "RECEIVED";
    public const WAREHOUSE_STATUS_DISPOSED = "DISPOSED";
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
}
