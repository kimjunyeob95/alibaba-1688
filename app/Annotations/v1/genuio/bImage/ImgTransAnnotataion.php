<?php

namespace App\Annotations\v1\genuio\bImage;

/**
 * 
 * 
 * @OA\SecurityScheme(
 *      securityScheme="BearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT"
 * )
 * 
 * @OA\Schema(
 *     schema="ImgTransSchema",
 *     required={"jobId", "images"},
 *     @OA\Property(
 *         property="jobId",
 *         type="string",
 *         example="1",
 *         description="작업 ID"
 *     ),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"id", "imgTransBase64"},
 *             @OA\Property(
 *                 property="id",
 *                 type="string",
 *                 example="1",
 *                 description="이미지 ID"
 *             ),
 *             @OA\Property(
 *                 property="imgTransBase64",
 *                 type="string",
 *                 example="~~",
 *                 description="Base64로 인코딩된 이미지 데이터"
 *             )
 *         ),
 *         description="번역 된 이미지 ID와 base64 인코딩된 이미지 데이터"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/genuio/img/trans",
 *     summary="번역 된 이미지 전송",
 *     tags={"이미지"},
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ImgTransSchema")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/SuccessResponse")
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


class ImgTransAnnotataion{
}
