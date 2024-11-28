<?php

namespace App\Constants;


class SlackConstant
{
    /** 9999_wms_정보알림 */
    public static function WMS_INFO_SLACK(): string
    {
        return env('WMS_INFO_SLACK');
    }
}
