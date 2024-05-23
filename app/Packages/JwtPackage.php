<?php

namespace App\Packages;

use App\Models\ApiUser;
use Carbon\Carbon;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Route;

class JwtPackage
{
    private array $returnMsg;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    public function tokenCreate(string $userId, string $user_company): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $user = ApiUser::where([
                'user_id'      => $userId,
                'user_company' => $user_company,
            ])->first();

            if (!$user) {
                throw new Exception("없는 아이디입니다.");
            }

            $result    = $this->createToken($user);
            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * 토큰 생성
     */
    private function createToken(ApiUser $user): array
    {
        $expirationTime = time() + (100 * 365 * 24 * 60 * 60); // 만료시간을 위한 Token 생성 시간 100년
        $data           = ["user" => $user];
        $payload        = [
            'iss'        => Route::currentRouteName(), // 발행자
            'exp'        => $expirationTime,
            'sub'        => $data, // 사용자 정의 데이터
            'created_at' => Carbon::now()
        ];

        return [
            'token'      => JWT::encode($payload, (string)env('JWT_SECRET'), 'HS256'),
            'expires_at' => Carbon::createFromTimestamp($expirationTime)->format('Y-m-d H:i:s')
        ];
    }
}
