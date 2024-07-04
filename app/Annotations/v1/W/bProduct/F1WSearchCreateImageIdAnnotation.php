<?php

namespace App\Annotations\v1\W\bProduct;

/**
 * 
 * @OA\Schema(
 *     schema="WSearchCreateImageIdSchema",
 *     required={"img_file"},
 *     @OA\Property(
 *         property="img_file",
 *         type="file",
 *         example="",
 *         description="이미지 파일 (파일 크기는 300KB 이하, 이미지 형식만 전송)"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/w/products/search/create/imageId",
 *     summary="W 상품 이미지 ID 생성",
 *     description="W 상품 이미지 ID 생성 endPoint",
 *     tags={"상품"},
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(ref="#/components/schemas/WSearchCreateImageIdSchema")
 *         )
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


class F1WSearchCreateImageIdAnnotation{
}
