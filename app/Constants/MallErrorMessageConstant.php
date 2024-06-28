<?php

namespace App\Constants;

class MallErrorMessageConstant
{
    private static $defaultMsg         = "(을)를 입력해주세요.";
    private static $defaultTypeMsg     = "의 타입형식이 올바르지 않습니다.";
    private static $defaultHaveMsg     = "(은)는 이미 등록되어 있습니다.";
    private static $defaultNotHaveMsg  = "Empty";
    private static $defaultFitErrorMsg = "Error";

    public const ERROR_MESSAGE_USER_ID            = "user_id";
    public const ERROR_MESSAGE_PRODUCT            = "product";
    public const ERROR_MESSAGE_NOT_TRANS_IMG      = "이미지 번역이 완료되지 않은 상품입니다.";
    public const ERROR_MESSAGE_NOT_MAPPING_CATE   = "카테고리 매핑이 완료되지 않은 상품입니다.";
    public const ERROR_MESSAGE_HAVE_REGIST        = "이미 전송한 상품입니다.";
    public const ERROR_MESSAGE_MODI_UNREGIST      = "등록되지 않은 상품입니다.";
    public const ERROR_MESSAGE_XML_PARSE          = "xml parse";
    public const ERROR_MESSAGE_PRODUCT_STATUS     = "전송 불가한 상품상태 입니다";
    public const ERROR_MESSAGE_TYPE               = "상품 구분이 설정되지 않았습니다";
    public const ERROR_MESSAGE_OPTION             = "상품 옵션이 없습니다";
    public const ERROR_MESSAGE_EASYSELL_GOODS_API = "easySell_Goods_Api";
    public const ERROR_MESSAGE_IMAGES             = "images";
    public const ERROR_MESSAGE_IMAGES_ORIGIN_URL  = "images origin_url";
    public const ERROR_MESSAGE_CHANNEL_QUEUE_ID   = "channel_queue_id";
    public const ERROR_MESSAGE_JOB_ID             = "job_id";
    public const ERROR_MESSAGE_IMAGE_ID           = "image_id";
    public const ERROR_MESSAGE_OFFER_ID           = "offer_id";
    public const ERROR_MESSAGE_IMG_ID             = "img_id";
    public const ERROR_MESSAGE_IMG_TYPE           = "img_type";
    public const ERROR_MESSAGE_IMAGE_BASE64       = "image_base64";
    public const ERROR_MESSAGE_CLEANED_BASE64     = "cleaned_base64";
    public const ERROR_MESSAGE_MEMBER_ID          = "member_id";
    public const ERROR_MESSAGE_OPTIONS            = "options";
    public const ERROR_MESSAGE_CATETYPE           = "cateType";
    public const ERROR_MESSAGE_CATEFIRST          = "cateFirst";
    public const ERROR_MESSAGE_CATEGORYNM         = "categoryNm";
    public const ERROR_MESSAGE_LEVEL              = "level";
    public const ERROR_MESSAGE_CATEGORYCODE       = "categoryCode";
    public const ERROR_MESSAGE_W_APP_MAPPINGCODE  = "WApp 맵핑 코드가 없습니다.";
    public const ERROR_MESSAGE_OC_MAPPINGCODE     = "onchannel 맵핑 코드가 없습니다.";
    public const ERROR_MESSAGE_OFFERIDS           = "offerIds";
    public const ERROR_MESSAGE_LOGID              = "logId";
    public const ERROR_MESSAGE_PRD_DESC_KR        = "상세설명 국문 번역 미완료";
    public const ERROR_MESSAGE_TRANS_STATUS       = "번역 미완료 상태";
    public const ERROR_MESSAGE_CHANNEL_TYPE       = "channel_type";
    public const ERROR_MESSAGE_CHANNEL_CODE       = "channel_code";
    public const ERROR_MESSAGE_W_PRD_COLLECT      = "W 상품 수집";
    public const ERROR_MESSAGE_MAIN_IMAGE         = "메인 이미지";
    public const ERROR_MESSAGE_MAIN_EN_IMAGE      = "메인 영문 이미지";
    public const ERROR_MESSAGE_SUB_IMAGE          = "서브 이미지";
    public const ERROR_MESSAGE_SUB_EN_IMAGE       = "서브 영문 이미지";

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
