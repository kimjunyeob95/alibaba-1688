<?php

namespace App\Constants;

class BonaeraErrorMessageConstant
{
    private static $defaultMsg         = "(을)를 입력해주세요.";
    private static $defaultTypeMsg     = "의 타입형식이 올바르지 않습니다.";
    private static $defaultHaveMsg     = "(은)는 이미 등록되어 있습니다.";
    private static $defaultNotHaveMsg  = "Empty";
    private static $defaultFitErrorMsg = "Error";

    public const ERROR_MESSAGE_BONAERA_IN_BASE_DATA      = "bonaera_in_base_data";
    public const ERROR_MESSAGE_BONAERA_IN_PRODUCT_DATA   = "bonaera_in_product_data";
    public const ERROR_MESSAGE_BONAERA_IN_FAIL_DATA      = "bonaera_in_fail_data";
    public const ERROR_MESSAGE_BONAERA_OUT_BASE_DATA     = "bonaera_out_base_data";
    public const ERROR_MESSAGE_BONAERA_OUT_DELIVERY_DATA = "bonaera_out_delivery_data";
    public const ERROR_MESSAGE_LOGISTICS_BILL_NO         = "logisticsBillNo";
    public const ERROR_MESSAGE_LOGISTICS_ITEMLIST        = "logistics itemList";
    public const ERROR_MESSAGE_HS_CODE                   = "hs_code";
    public const ERROR_MESSAGE_PRODUCTSHNO               = "품목번호";
    public const ERROR_MESSAGE_ITEMLIST                  = "itemList";
    public const ERROR_MESSAGE_OPTLIST                   = "optList";
    public const ERROR_MESSAGE_ORDERCHANNELOBJS          = "orderChannelObjs";
    public const ERROR_MESSAGE_ALL_OPTION_NOT_READY      = "모든 옵션이 입고완료 상태가 아닙니다.";
    public const ERROR_MESSAGE_TYPE                      = "type";
    public const ERROR_MESSAGE_STOCK_NO                  = "stock_no";
    public const ERROR_MESSAGE_GROUP_NO                  = "group_no";
    public const ERROR_MESSAGE_SH_NOS                    = "sh_nos";
    public const ERROR_MESSAGE_SH_NO                     = "sh_no";
    public const ERROR_MESSAGE_ORIGIN_GROUP_NO           = "origin_group_no";
    public const ERROR_MESSAGE_CHANGE_GROUP_NO           = "change_group_no";
 
    public static function getErrorMessageNotDefault($constantName): string
    {
        $errorMessage = constant('self::ERROR_MESSAGE_' . $constantName);
        return $errorMessage;
    }

    public static function getErrorMessage($constantName): string
    {
        $errorMessage = constant('self::ERROR_MESSAGE_' . $constantName);
        return $errorMessage . self::$defaultMsg;
    }

    public static function getTypeErrorMessage($constantName): string
    {
        $errorMessage = constant('self::ERROR_MESSAGE_' . $constantName);
        return $errorMessage . self::$defaultTypeMsg;
    }

    public static function getHaveErrorMessage(string $constantName): string
    {
        $errorMessage = constant('self::ERROR_MESSAGE_' . $constantName);
        return $errorMessage . self::$defaultHaveMsg;
    }

    public static function getNotHaveErrorMessage(string $constantName): string
    {
        $errorMessage = constant('self::ERROR_MESSAGE_' . $constantName);
        return self::$defaultNotHaveMsg . " " . $errorMessage;
    }

    public static function getFitErrorMessage(string $constantName): string
    {
        $errorMessage = constant('self::ERROR_MESSAGE_' . $constantName);
        return self::$defaultFitErrorMsg . " " . $errorMessage;
    }
}
