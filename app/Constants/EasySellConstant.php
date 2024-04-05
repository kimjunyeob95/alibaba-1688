<?php

namespace App\Constants;


class EasySellConstant
{
    //상품 모드
    public const ITEM_REGIST = "I"; //등록
    public const ITEM_MODI = "M"; //수정

    //셀러허브 아이디
    public const SELLERHUB_ID = "sellerhub";

    //api 기본정보
    public const LINKER_ID = "onchannel";
    public const USER_ID   = "2018637410_200"; //W_패션
    public const USER_PW   = "1234";

    // 상품 판매 상태
    public const STATUS_ON_SALE   = "001";
    public const STATUS_STOP_SALE = "004";

    public const ITEM_MADE_IN = "중국";

    public const API_SUCCESS = "SUCC";
    public const API_FAIL    = "FAIL";

    public const API_REGIST_SUCCESS = "Y";
    public const API_REGIST_FAIL    = "N";
}
