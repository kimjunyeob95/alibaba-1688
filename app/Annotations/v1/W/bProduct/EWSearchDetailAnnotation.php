<?php

namespace App\Annotations\v1\W\bProduct;

/**
 * 
 * 
 *
 * @OA\Get(
 *     path="/api/w/products/search/detail/{offerId}",
 *     summary="W 상품 상세 조회",
 *     description="W 상품 상세 조회 endPoint",
 *     tags={"상품"},
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="offerId",
 *         in="path",
 *         required=true,
 *         description="제품 ID",
 *         @OA\Schema(
 *             type="intger",
 *             example="539753036122"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="country",
 *         in="query",
 *         required=true,
 *         description="언어 ko: 국문, en: 영문",
 *         @OA\Schema(
 *             type="string",
 *             example="ko"
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


class EWSearchDetailAnnotation{
}
