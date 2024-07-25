<?php

namespace App\Constants;

class OrderErrorMessageConstant
{
    private static $defaultMsg         = "(을)를 입력해주세요.";
    private static $defaultTypeMsg     = "의 타입형식이 올바르지 않습니다.";
    private static $defaultHaveMsg     = "(은)는 이미 등록되어 있습니다.";
    private static $defaultNotHaveMsg  = "Empty";
    private static $defaultFitErrorMsg = "Error";

    public const ERROR_MESSAGE_CARGOPARAMLIST         = "주문 생성 제품 정보";
    public const ERROR_MESSAGE_OFFER_ID               = "offer_id";
    public const ERROR_MESSAGE_CHANNEL_ORDER_ID       = "채널 주문 번호";
    public const ERROR_MESSAGE_SPEC_ID                = "제품 spec ID";
    public const ERROR_MESSAGE_OPTION_ID              = "option_id";
    public const ERROR_MESSAGE_QUANTITY               = "제품 수량";
    public const ERROR_MESSAGE_PRICE                  = "price";
    public const ERROR_MESSAGE_RECEIVE_NAME           = "수취인 이름";
    public const ERROR_MESSAGE_RECEIVE_TELL           = "수취인 전화번호";
    public const ERROR_MESSAGE_RECEIVE_PHONE          = "수취인 휴대폰번호";
    public const ERROR_MESSAGE_PRODUCTPARAMLIST       = "productParamList";
    public const ERROR_MESSAGE_OPTIONPARAMLIST        = "option_param_list";
    public const ERROR_MESSAGE_OPTION_PRICE           = "option_price";
    public const ERROR_MESSAGE_BUYER_NAME             = "buyer_name";
    public const ERROR_MESSAGE_BUYER_CLEARANCE_NUMBER = "buyer_clearance_number";
    public const ERROR_MESSAGE_BUYER_NUMBER           = "buyer_number";
    public const ERROR_MESSAGE_BUYER_PHONE            = "buyer_phone";
    public const ERROR_MESSAGE_BUYER_ZIPCODE          = "buyer_zipcode";
    public const ERROR_MESSAGE_BUYER_ADDRESS          = "buyer_address";
    public const ERROR_MESSAGE_BUYER_MEMO             = "buyer_memo";
    public const ERROR_MESSAGE_START_QUANTITY         = "최소 구매 수량 이상으로 주문을 요청해주세요.";
    public const ERROR_MESSAGE_ORDER                  = "Order";
    public const ERROR_MESSAGE_TOTAL_QUANTITY         = "총 주문수량";
    public const ERROR_MESSAGE_W_DETAIL               = "W 주문 detail 조회";
    public const ERROR_MESSAGE_BASE_INFO              = "baseInfo";
    public const ERROR_MESSAGE_ORDER_ID               = "orderId";
    public const ERROR_MESSAGE_CHANNEL_PRICE          = "channel_price";
    public const ERROR_MESSAGE_CHANNEL_PRICES         = "channel_prices";
    public const ERROR_MESSAGE_SPEC_IDS               = "spec_ids";
    public const ERROR_MESSAGE_SKU_IDS                = "sku_ids";
    public const ERROR_MESSAGE_ORDER_CHANNEL          = "order_channel";
    public const ERROR_MESSAGE_PRODUCT                = "product";
    public const ERROR_MESSAGE_OPTION                 = "option";
    public const ERROR_MESSAGE_DELIVERY_PRICE         = "delivery_price";
    public const ERROR_MESSAGE_STATUS_WAITBUYERPAY    = "주문대기";
    public const ERROR_MESSAGE_CANCEL_API             = "주문 취소 API";
 
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
