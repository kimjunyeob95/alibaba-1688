<?php

namespace App\Annotations\v1\genuio\bProduct;

/**
 * 
 * @OA\Schema(
 *     schema="ProductsUpdateImagesSchema",
 *     required={"images"},
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"id", "base64"},
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 example=64,
 *                 description="이미지 ID"
 *             ),
 *             @OA\Property(
 *                 property="base64",
 *                 type="string",
 *                 example="~~",
 *                 description="Base64로 인코딩된 이미지 데이터"
 *             )
 *         ),
 *         description="WApp 상품 AI 이미지 추가를 위한 payload"
 *     )
 * )
 * 
 * 
 * @OA\Schema(
 *     schema="ProductsUpdateImagesSuccessResponse",
 *     @OA\Property(property="status", type="integer", example=200),
 *     @OA\Property(
 *         property="meta", 
 *         type="object",
 *         @OA\Property(property="timestamp", type="string", example="2023-12-19 17:45:50"),
 *         @OA\Property(property="apiType", type="string", example="mall")
 *     ),
 *     @OA\Property(
 *         property="data", 
 *         type="array",
 *         @OA\Items(
 *            type="object",
 *            @OA\Property(
 *                property="id",
 *                type="integer",
 *                description="이미지 ID",
 *                example=64
 *            ),
 *            @OA\Property(
 *                property="isSuccess",
 *                type="boolean",
 *                description="업데이트 성공 여부",
 *                example=true
 *            ),
 *            @OA\Property(
 *                property="msg",
 *                type="string",
 *                description="실패 사유",
 *                example="Empty image"
 *            ),
 *         ),
 *      )
 * )
 * 
 *
 * @OA\Patch(
 *     path="/api/genuio/products/{offerId}/images",
 *     summary="상품 AI 이미지 추가",
 *     description="WApp 상품 AI 이미지 추가 endPoint",
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
 *         @OA\JsonContent(ref="#/components/schemas/ProductsUpdateImagesSchema")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/ProductsUpdateImagesSuccessResponse")
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


class BpatchImgAnnotataion{
}
