<?php

namespace App\Constants;


class GenuioConstant
{
    /** 이미지 수정 */
    public const IMG_TRANS       = "imgTrans";
    public const IMG_TRANS_AGAIN = "imgTransAgain";
    public const IMG_Ai_TRANS    = "imgAiTrans";
    public const IMG_TRANS_TYPE = [
        self::IMG_TRANS       => "최초 이미지 번역",
        self::IMG_TRANS_AGAIN => "이미지 재번역",
        self::IMG_Ai_TRANS    => "AI 번역",
    ];

    public const IS_ORIGIN_Y = "Y";
    public const IS_ORIGIN_N = "N";

    /** 양방향 통신 큐 우선순위 설정 */
    public const PRIORITY_TRUE  = true;
    public const PRIORITY_FALSE = false;

    /** 큐 제거 응답값 */
    public const REMOVE_QUEUE_OK  = "ok";

    /** AI 알고리즘 적용 위치 */
    public const AI_APPLY_DESC_KR = "desc_kr";
    public const AI_APPLY_DESC_EN = "desc_en";

    /** 콜백 통신 여부 */
    public const CALLBACK_Y     = "Y";
    public const CALLBACK_N     = "N";
    public const CALLBACK_TYPE = [
        self::CALLBACK_Y => "통신 완료",
        self::CALLBACK_N => "통신 미완료",
    ];

}
