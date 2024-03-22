<?php

namespace App\Constants;


class OpenApiConstant
{
    // 회사 리스트
    public const API_USER_COMPANY_GENUIO = "genuio";
    public const API_USER_COMPANY_OC     = "onchannel";

    // queue 상태
    public const QUEUE_STAY = "S";     // 대기중
    public const QUEUE_SUCCESS = "Y";  // 성공
    public const QUEUE_FAIL = "N";     // 실패


    private static $defaultMsg         = "(을)를 입력해주세요.";
    private static $defaultTypeMsg     = "의 타입형식이 올바르지 않습니다.";
    private static $defaultHaveMsg     = "(은)는 이미 등록되어 있습니다.";
    private static $defaultNotHaveMsg  = "Empty";
    private static $defaultFitErrorMsg = "Error";

    public const ERROR_MESSAGE_QUEUE_ID              = "queue ID";
    public const ERROR_MESSAGE_NOT_EQUAL_COUNT_IMAGE = "요청과 응답의 이미지 개수가 다릅니다.";
    public const ERROR_MESSAGE_TRANS_REQUEST_IMAGE   = "Trans Request Image";
 
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
