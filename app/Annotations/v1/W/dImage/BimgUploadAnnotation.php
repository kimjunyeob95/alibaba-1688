<?php

namespace App\Annotations\v1\W\dImage;

/**
 * 
 * @OA\Schema(
 *     schema="ImageUploadSchema",
 *     required={"member_id", "images"},
 *     @OA\Property(
 *         property="member_id",
 *         type="string",
 *         example="tester123",
 *         description="판매자 계정 이름"
 *     ),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"id", "base64"},
 *             @OA\Property(
 *                  property="id",
 *                  type="integer",
 *                  example=1,
 *                  description="채널에서 관리하는 이미지 고유 ID"
 *              ),
 *              @OA\Property(
 *                  property="base64",
 *                  type="string",
 *                  example="~~~",
 *                  description="이미지 파일 base64 인코딩"
 *              ),
 *         ),
 *         description="이미지 정보"
 *     )
 * )
 * 
 *
 * @OA\Post(
 *     path="/api/mall/{channel}/img/upload",
 *     @OA\Parameter(
 *         name="channel",
 *         in="path",
 *         required=true,
 *         description="채널 ID",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     summary="이미지 S3 업로드",
 *     description="이미지 S3 업로드 endPoint",
 *     tags={"이미지"},
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ImageUploadSchema"),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/SuccessResponse"),
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Authorization Error",
 *         @OA\JsonContent(ref="#/components/schemas/401ErrorResponse")
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


class BimgUploadAnnotation{
}
