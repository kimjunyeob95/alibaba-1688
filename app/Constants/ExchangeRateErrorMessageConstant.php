<?php

namespace App\Constants;

class ExchangeRateErrorMessageConstant
{
    private static $defaultMsg         = "(을)를 입력해주세요.";
    private static $defaultTypeMsg     = "의 타입형식이 올바르지 않습니다.";
    private static $defaultHaveMsg     = "(은)는 이미 등록되어 있습니다.";
    private static $defaultNotHaveMsg  = "Empty";
    private static $defaultFitErrorMsg = "Error";

    public const ERROR_MESSAGE_EXCHANGEOBJ        = "환율 정보";
    public const ERROR_MESSAGE_ALEADY_EXCHANGEOBJ = "이미 수집 된 환율 기록";
    public const ERROR_MESSAGE_RESPONSE           = "API 응답";
    public const ERROR_MESSAGE_DATE_FORMAT        = "날짜가 YYYYMMDD 형식이 아닙니다.";
 
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
