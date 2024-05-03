<?php

namespace App\Constants;

class ProductErrorMessageConstant
{
    private static $defaultMsg         = "(을)를 입력해주세요.";
    private static $defaultTypeMsg     = "의 타입형식이 올바르지 않습니다.";
    private static $defaultHaveMsg     = "(은)는 이미 등록되어 있습니다.";
    private static $defaultNotHaveMsg  = "Empty";
    private static $defaultFitErrorMsg = "Error";

    public const ERROR_MESSAGE_PRODUCT                                 = "Product";
    public const ERROR_MESSAGE_PRODUCT_SEARCH_KEYWORDQUERY             = "product.search.keywordQuery";
    public const ERROR_MESSAGE_PRODUCT_SEARCH_IMAGEQUERY               = "product.search.imageQuery";
    public const ERROR_MESSAGE_PRODUCT_SEARCH_QUERYPRODUCTDETAIL       = "product.search.queryProductDetail";
    public const ERROR_MESSAGE_PRODUCT_SEARCH_QUERYPRODUCTDETAIL_W2_KR = "product.search.queryProductDetail.W2.KR";
    public const ERROR_MESSAGE_PRODUCT_SEARCH_QUERYPRODUCTDETAIL_W2_EN = "product.search.queryProductDetail.W2.EN";
    public const ERROR_MESSAGE_PRODUCT_SKUINFOS                        = "productSkuInfos";
    public const ERROR_MESSAGE_PRODUCT_PRICE_1688                      = "1688 Price";
    public const ERROR_MESSAGE_PRODUCT_CONSIGN_PRICE                   = "productSkuInfos consignPrice";
    public const ERROR_MESSAGE_PRODUCT_TRANS_IMG                       = "Genuio imageTranslate";
    public const ERROR_MESSAGE_PRODUCT_S3_IMG_UPLOAD                   = "S3 image Upload";
    public const ERROR_MESSAGE_PRODUCT_CHECK_IMG_SIZE                  = "check img size";
    public const ERROR_MESSAGE_PRODUCT_KEYWORD                         = "keyword";
    public const ERROR_MESSAGE_PRODUCT_MAIN_IMG                        = "Main Img";
    public const ERROR_MESSAGE_SEARCH_TITLE                            = "search_title";
    public const ERROR_MESSAGE_OFFER_IDS                               = "offer ids";
    public const ERROR_MESSAGE_MD_PRICE                                = "MD price";
    public const ERROR_MESSAGE_STATUS                                  = "status";
    public const ERROR_MESSAGE_ALREADY_PRODUCT                         = "이미 수집 된 상품입니다.";
 
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
