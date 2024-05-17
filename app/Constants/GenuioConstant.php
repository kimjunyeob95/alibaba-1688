<?php

namespace App\Constants;


class GenuioConstant
{
    /** 이미지 수정 */
    public const IMG_TRANS       = "imgTrans";
    public const IMG_TRANS_AGAIN = "imgTransAgain";
    public const IMG_Ai_TRANS    = "imgAiTrans";

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

}
