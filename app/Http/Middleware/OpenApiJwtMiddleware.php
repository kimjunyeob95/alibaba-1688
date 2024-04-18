<?php

namespace App\Http\Middleware;

use App\Constants\HttpConstant;
use App\Models\ApiUser;
use Carbon\Carbon;
use Closure;
use Exception;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

class OpenApiJwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return helpers_json_response(HttpConstant::UNAUTHORIZED, [], "토큰을 전달해주세요.");
            }

            $decodedToken = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            // 토큰이 유효한지 확인
            if ($this->isTokenExpired($decodedToken)) {
                return helpers_json_response(HttpConstant::UNAUTHORIZED, [], "토큰의 유효시간이 만료되었습니다.");
            }

            // 마지막 생성 토큰인지 확인
            // if (!$this->isLastToken($decodedToken)) {
            //     return helpers_json_response(HttpConstant::UNAUTHORIZED, [], "마지막 생성 토큰을 전달해주세요.");
            // }

            return $next($request);
        } catch (Exception $e) {
            $message = $e->getMessage();
            if( $e->getMessage() == "Expired token" ){
                $message = "토큰의 유효시간이 만료되었습니다.";
            }
            return helpers_json_response(HttpConstant::INTERNAL_SERVER_ERROR, [], $message);
        }
       
    }

    private function isTokenExpired(stdClass $decodedToken)
    {
        $expirationTime = $decodedToken->exp;
        $currentTime    = time();

        return $expirationTime < $currentTime;
    }

    private function isLastToken(stdClass $decodedToken): bool
    {
        $userId      = $decodedToken->sub->user->user_id;
        $userCompany = $decodedToken->sub->user->user_company;
        $createdAt   = Carbon::parse($decodedToken->created_at)
                        ->timezone(env("APP_TIMEZONE", "Asia/Seoul"))
                        ->startOfMinute(); // 초를 0으로 설정

        $getApiUserObj = ApiUser::where([
            'user_id'      => $userId,
            'user_company' => $userCompany,
        ])->first();

        if( $createdAt == $getApiUserObj->updated_at->startOfMinute() ){
            return true;
        }
        return false;
    }
}
