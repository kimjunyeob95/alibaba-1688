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
    public const ERROR_MESSAGE_OPTION                                  = "Option";
    public const ERROR_MESSAGE_PRODUCT_SEARCH_KEYWORDQUERY             = "product.search.keywordQuery";
    public const ERROR_MESSAGE_PRODUCT_SEARCH_IMAGEQUERY               = "product.search.imageQuery";
    public const ERROR_MESSAGE_PRODUCT_SEARCH_QUERYPRODUCTDETAIL       = "product.search.queryProductDetail";
    public const ERROR_MESSAGE_SEARCH_QUERYPRODUCTDETAIL_EN            = "product.search.queryProductDetail_EN";
    public const ERROR_MESSAGE_PRODUCT_SEARCH_QUERYPRODUCTDETAIL_W2_KR = "product.search.queryProductDetail.W2.KR";
    public const ERROR_MESSAGE_PRODUCT_SEARCH_QUERYPRODUCTDETAIL_W2_EN = "product.search.queryProductDetail.W2.EN";
    public const ERROR_MESSAGE_PRODUCT_SKUINFOS                        = "productSkuInfos";
    public const ERROR_MESSAGE_PRICE_1688                              = "1688 Price";
    public const ERROR_MESSAGE_PRODUCT_CONSIGN_PRICE                   = "productSkuInfos consignPrice";
    public const ERROR_MESSAGE_PRODUCT_TRANS_IMG                       = "Genuio imageTranslate";
    public const ERROR_MESSAGE_PRODUCT_S3_IMG_UPLOAD                   = "S3 image Upload";
    public const ERROR_MESSAGE_PRODUCT_CHECK_IMG_SIZE                  = "check img size";
    public const ERROR_MESSAGE_PRODUCT_KEYWORD                         = "keyword";
    public const ERROR_MESSAGE_PRODUCT_MAIN_IMG                        = "Main Img";
    public const ERROR_MESSAGE_SEARCH_TITLE                            = "search_title";
    public const ERROR_MESSAGE_OFFER_ID                                = "offer id";
    public const ERROR_MESSAGE_OFFER_IDS                               = "offer ids";
    public const ERROR_MESSAGE_MD_PRICE                                = "MD price";
    public const ERROR_MESSAGE_STATUS                                  = "status";
    public const ERROR_MESSAGE_ALREADY_PRODUCT                         = "이미 수집 된 상품입니다.";
    public const ERROR_MESSAGE_PRODUCT_EXCEPT                          = "판매제외 상품";
    public const ERROR_MESSAGE_PRD_NAME_KR                             = "prd_name_kr";
    public const ERROR_MESSAGE_PRD_NAME_EN                             = "prd_name_en";
    public const ERROR_MESSAGE_OPTIONLIST                              = "optionList";
    public const ERROR_MESSAGE_OPTIONLIST_ID                           = "optionList id";
    public const ERROR_MESSAGE_OPTIONLIST_IS_EXCEPT                    = "optionList is_except";
    public const ERROR_MESSAGE_OPTIONLIST_OPTION_NAME_KR               = "optionList option_name_kr";
    public const ERROR_MESSAGE_OPTIONLIST_OPTION_NAME_EN               = "optionList option_name_en";
    public const ERROR_MESSAGE_GOSILIST                                = "gosiList";
    public const ERROR_MESSAGE_GOSILIST_ID                             = "gosiList id";
    public const ERROR_MESSAGE_GOSILIST_IS_EXCEPT                      = "gosiList is_except";
    public const ERROR_MESSAGE_GOSIKRLIST                              = "gosiKrList";
    public const ERROR_MESSAGE_GOSIKRLIST_ID                           = "gosiKrList id";
    public const ERROR_MESSAGE_GOSIKRLIST_IS_EXCEPT                    = "gosiKrList is_except";
    public const ERROR_MESSAGE_GOSIENLIST                              = "gosiEnList";
    public const ERROR_MESSAGE_GOSIENLIST_ID                           = "gosiEnList id";
    public const ERROR_MESSAGE_GOSIENLIST_IS_EXCEPT                    = "gosiEnList is_except";
    public const ERROR_MESSAGE_INSPECT_IMG_STATUS                      = "inspect_img_status";
    public const ERROR_MESSAGE_INSPECT_PRD_STATUS                      = "inspect_prd_status";
    public const ERROR_MESSAGE_INSPECT_GOSI_STATUS                     = "inspect_gosi_status";
    public const ERROR_MESSAGE_OPTION_QUANTITY                         = "Option quantity";
    public const ERROR_MESSAGE_WEIGHT                                  = "weight";
    public const ERROR_MESSAGE_ATTRIBUTE_IDS                           = "attribute_ids";
    public const ERROR_MESSAGE_APPLY_ATTRIBUTE_NAME                    = "apply_attribute_name";
    public const ERROR_MESSAGE_KEYWORD                                 = "keyword";
    public const ERROR_MESSAGE_BEGINPAGE                               = "beginPage";
    public const ERROR_MESSAGE_PAGESIZE                                = "pageSize";
    public const ERROR_MESSAGE_SORT                                    = "sort";
    public const ERROR_MESSAGE_COUNTRY                                 = "country";
    public const ERROR_MESSAGE_IMG_FILE                                = "img_file";
    public const ERROR_MESSAGE_IMG_ID                                  = "img_id";
    public const ERROR_MESSAGE_PRODUCT_SEARCH_OFFERRECOMMEND           = "product.search.offerRecommend";
    public const ERROR_MESSAGE_PRODUCT_RELATED_RECOMMEND               = "product.related.recommend";
 
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
