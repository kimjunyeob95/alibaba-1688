<?php

namespace App\Constants;

class ImageErrorMessageConstant
{
    private static $defaultMsg         = "(을)를 입력해주세요.";
    private static $defaultTypeMsg     = "의 타입형식이 올바르지 않습니다.";
    private static $defaultHaveMsg     = "(은)는 이미 등록되어 있습니다.";
    private static $defaultNotHaveMsg  = "Empty";
    private static $defaultFitErrorMsg = "Error";

    public const ERROR_MESSAGE_FILE          = "이미지 파일";
    public const ERROR_MESSAGE_TYPE          = "업로드된 파일이 이미지 형식이 아닙니다.";
    public const ERROR_MESSAGE_SIZE          = "파일 크기는 300KB 이하로 업로드 가능합니다.";
    public const ERROR_MESSAGE_IMG_ID        = "이미지ID";
    public const ERROR_MESSAGE_IMAGE         = "image";
    public const ERROR_MESSAGE_IMAGES        = "images";
    public const ERROR_MESSAGE_IMAGES_ID     = "images id";
    public const ERROR_MESSAGE_IMAGES_AI_ID  = "images Ai id";
    public const ERROR_MESSAGE_IMAGES_BASE64 = "images base64";
    public const ERROR_MESSAGE_S3_IMG_UPLOAD = "S3 image Upload";
    public const ERROR_MESSAGE_EXCEPT_IMG    = "제외 이미지로 인한 패스";
    public const ERROR_MESSAGE_W_IMAGE_ID    = "w image id";
    public const ERROR_MESSAGE_W_IMAGE_QUERY = "w image query";

 
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
