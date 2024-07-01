<?php

namespace App\Constants;

class CategoryErrorMessageConstant
{
    private static $defaultMsg         = "(을)를 입력해주세요.";
    private static $defaultTypeMsg     = "의 타입형식이 올바르지 않습니다.";
    private static $defaultHaveMsg     = "(은)는 이미 등록되어 있습니다.";
    private static $defaultNotHaveMsg  = "Empty";
    private static $defaultFitErrorMsg = "Error";

    public const ERROR_MESSAGE_PARENT_CATEGORY     = "최상위 카테고리가 없습니다.";
    public const ERROR_MESSAGE_NOT_PARENT_CATEGORY = "최상위 카테고리가 아닙니다.";
    public const ERROR_MESSAGE_MAPPING_CATEGORY    = "맵핑 카테고리";
    public const ERROR_MESSAGE_CATEGORY            = "category";
    public const ERROR_MESSAGE_CATEGORY_TREE       = "categoryTree";
    public const ERROR_MESSAGE_CATEGORYID          = "1688 category id";
    public const ERROR_MESSAGE_CATEGORYIDS         = "1688 category ids";
    public const ERROR_MESSAGE_W_CATEGORYID        = "W category id";
    public const ERROR_MESSAGE_LEVEL               = "level";
    public const ERROR_MESSAGE_CATE_FIRST          = "cate_first";
    public const ERROR_MESSAGE_CATEGORY_NAME       = "category name";
    public const ERROR_MESSAGE_WEIGHT              = "weight";
    public const ERROR_MESSAGE_CHANNELCATECODE     = "channelCateCode";
    public const ERROR_MESSAGE_SEARCH_TOPKEYWORD   = "product.search.topKeyword";
    public const ERROR_MESSAGE_OCPUBLIC            = "ocPublic";
    public const ERROR_MESSAGE_OCPRIVATE           = "ocPrivate";
    public const ERROR_MESSAGE_ESW                 = "esW";
    public const ERROR_MESSAGE_ESDROPHUB           = "esDropHub";
    public const ERROR_MESSAGE_CATEPARAMS          = "cateParams";
 
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
