<?php

namespace App\Constants;


class LogConstant
{
    // 수집 진행 단계
    public const COLLECT_STAY     = "S";
    public const COLLECT_RUNNING  = "R";
    public const COLLECT_COMPLETE = "C";
    public const COLLECT_STATUS = [
        self::COLLECT_STAY     => "대기",
        self::COLLECT_RUNNING  => "수집중",
        self::COLLECT_COMPLETE => "완료",
    ];

    // 수집 성공 여부
    public const COLLECT_DETAIL_Y = "Y"; // 성공
    public const COLLECT_DETAIL_N = "N"; // 실패
    public const COLLECT_DETAIL_STATUS = [
        self::COLLECT_DETAIL_Y => "성공",
        self::COLLECT_DETAIL_N => "실패",
    ];

    // 수집 API 분류
    public const COLLECT_API_OFFERID          = "offerID";
    public const RE_COLLECT_API_OFFERID       = "reOfferID";
    public const COLLECT_API_KEYWORDQUERY     = "keywordQuery";
    public const COLLECT_API_IMAGEQUERY       = "imageQuery";
    public const COLLECT_API_URLQUERY         = "urlQuery";
    public const COLLECT_API_KEYWORDQUERY_ALL = "keywordQueryAll";
    public const COLLECT_API_IMAGEQUERY_ALL   = "imageQueryAll";
    public const COLLECT_MISS_PRODUCT         = "missProduct";
    public const COLLECT_API                  = [
        self::COLLECT_API_OFFERID          => "상품 ID 수집",
        self::RE_COLLECT_API_OFFERID       => "상품 ID 재 수집",
        self::COLLECT_API_KEYWORDQUERY     => "기본 정보 수집",
        self::COLLECT_API_IMAGEQUERY       => "Image 수집",
        self::COLLECT_API_KEYWORDQUERY_ALL => "기본 정보 전체 수집",
        self::COLLECT_API_IMAGEQUERY_ALL   => "Image 전체 수집",
        self::COLLECT_API_URLQUERY         => "상품상세 URL 수집",
        self::COLLECT_MISS_PRODUCT         => "정보누락 상품 수집",
    ];
}
