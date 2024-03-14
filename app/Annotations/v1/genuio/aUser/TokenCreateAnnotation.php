<?php

namespace App\Annotations\v1\genuio\aUser;

/**
 * 
 * @OA\Schema(
 *     schema="TokenCreateSchema",
 *     required={"user_id"},
 *     @OA\Property(property="user_id", type="string", example="genuio"),
 * )
 * @OA\Schema(
 *     schema="SuccessTokenResponse",
 *     @OA\Property(property="status", type="integer", example=200),
 *     @OA\Property(
 *         property="meta", 
 *         type="object",
 *         @OA\Property(property="timestamp", type="string", example="2023-12-19 17:45:50"),
 *         @OA\Property(property="apiVersion", type="string", example="v1")
 *     ),
 *     @OA\Property(property="msg", type="string", example="정상 처리 되었습니다."),
 *     @OA\Property(
 *         property="data", 
 *         type="object",
 *         @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
 *         @OA\Property(property="expiresAt", type="string", example="2023-12-19 18:45:50")
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/genuio/token/create",
 *     summary="토큰 생성",
 *     tags={"회원"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/TokenCreateSchema")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/SuccessTokenResponse")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input",
 *         @OA\JsonContent(ref="#/components/schemas/400ErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(ref="#/components/schemas/500ErrorResponse")
 *     )
 * )
*/


class TokenCreateAnnotation{
}
