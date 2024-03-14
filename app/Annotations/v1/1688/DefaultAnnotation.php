<?php

namespace App\Annotations\v1\genuio;

/**
 *
 *
 * @OA\Info(
 *  title="mslabe API",
 *  version="1.0",
 * )
 *
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     @OA\Property(property="status", type="integer", example=200),
 *     @OA\Property(
 *         property="meta", 
 *         type="object",
 *         @OA\Property(property="timestamp", type="string", example="2023-12-19 17:45:50"),
 *         @OA\Property(property="apiVersion", type="string", example="v1")
 *     ),
 *     @OA\Property(
 *         property="data", 
 *         type="object"
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="400ErrorResponse",
 *     @OA\Property(property="status", type="integer", example=400),
 *     @OA\Property(
 *         property="meta", 
 *         type="object",
 *         @OA\Property(property="timestamp", type="string", example="2023-12-19 17:45:50"),
 *         @OA\Property(property="apiVersion", type="string", example="v1")
 *     ),
 *     @OA\Property(
 *         property="error", 
 *         type="object",
 *         @OA\Property(property="code", type="integer", example=400),
 *         @OA\Property(property="message", type="string", example="비밀번호를 다시 확인해주세요.")
 *     ),
 * )
 * 
 * @OA\Schema(
 *     schema="401ErrorResponse",
 *     @OA\Property(property="status", type="integer", example=401),
 *     @OA\Property(
 *         property="meta", 
 *         type="object",
 *         @OA\Property(property="timestamp", type="string", example="2023-12-19 17:45:50"),
 *         @OA\Property(property="apiVersion", type="string", example="v1")
 *     ),
 *     @OA\Property(
 *         property="error", 
 *         type="object",
 *         @OA\Property(property="code", type="integer", example=401),
 *         @OA\Property(property="message", type="string", example="토큰을 전달해주세요.")
 *     ),
 * )
 * 
 * @OA\Schema(
 *     schema="404ErrorResponse",
 *     @OA\Property(property="status", type="integer", example=404),
 *     @OA\Property(
 *         property="meta", 
 *         type="object",
 *         @OA\Property(property="timestamp", type="string", example="2023-12-19 17:45:50"),
 *         @OA\Property(property="apiVersion", type="string", example="v1")
 *     ),
 *     @OA\Property(
 *         property="error", 
 *         type="object",
 *         @OA\Property(property="code", type="integer", example=404),
 *         @OA\Property(property="message", type="string", example="잘못된 접근입니다.")
 *     ),
 * )
 * 
 * @OA\Schema(
 *     schema="500ErrorResponse",
 *     @OA\Property(property="status", type="integer", example=500),
 *     @OA\Property(
 *         property="meta", 
 *         type="object",
 *         @OA\Property(property="timestamp", type="string", example="2023-12-19 17:45:50"),
 *         @OA\Property(property="apiVersion", type="string", example="v1")
 *     ),
 *     @OA\Property(
 *         property="error", 
 *         type="object",
 *         @OA\Property(property="code", type="integer", example=500),
 *         @OA\Property(property="message", type="string", example="Too many requests")
 *     ),
 * )
 * 
*/

class DefaultAnnotation{
}
