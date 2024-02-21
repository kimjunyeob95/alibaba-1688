<?php

namespace App\Constants;

class HttpConstant
{
    public const OK                    = 200; // 성공
    public const BAD_REQUEST           = 400; // 잘못된 요청
    public const UNAUTHORIZED          = 401; // 인증 오류
    public const FORBIDDEN             = 403; // 권한이 없는 오류
    public const NOT_FOUND             = 404; // 리소스 찾을 수 없음
    public const INTERNAL_SERVER_ERROR = 500; // 서버 오류

    public const ERRORS = [
        self::BAD_REQUEST,
        self::UNAUTHORIZED,
        self::FORBIDDEN,
        self::NOT_FOUND,
        self::INTERNAL_SERVER_ERROR
    ];

    public const ERROR_MESSAGE_NOT_FOUND             = "잘못된 접근입니다.";
    public const ERROR_MESSAGE_INTERNAL_SERVER_ERROR = "관리자에 문의 바랍니다.";
    public const ERROR_MESSAGE_TOO_MANY_REQUEST      = "해당 엔드포인트의 요청가능 횟수를 초과했습니다.";
}
