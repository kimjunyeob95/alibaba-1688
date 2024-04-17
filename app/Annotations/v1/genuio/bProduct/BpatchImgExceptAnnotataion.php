<?php

namespace App\Annotations\v1\genuio\bProduct;

/**
 * 
 * @OA\Schema(
 *     schema="ProductsUpdateImagesExceptSchema",
 *     required={"imgIds", "is_except"},
 *     @OA\Property(
 *         property="imgIds",
 *         type="array",
 *         @OA\Items(
 *             type="integer",
 *             example=64,
 *             description="이미지 ID"
 *         ),
 *         description="상품 이미지 제외를 위한 payload"
 *     ),
 *     @OA\Property(
 *         property="is_except",
 *         type="string",
 *         example="Y",
 *         description="Y: 제외, N: 제외취소"
 *     )
 * )
 * 
 * @OA\Patch(
 *     path="/api/genuio/products/{offerId}/images/except",
 *     summary="상품 이미지 제외 처리",
 *     description="WApp 상품 이미지 제외 처리 endPoint",
 *     tags={"상품"},
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="offerId",
 *         in="path",
 *         required=true,
 *         description="제품 ID",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ProductsUpdateImagesExceptSchema")
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


class BpatchImgExceptAnnotataion{
}
