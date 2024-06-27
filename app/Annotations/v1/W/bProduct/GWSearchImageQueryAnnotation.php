<?php

namespace App\Annotations\v1\W\bProduct;

/**
 * 
 * 
 *
 * @OA\Get(
 *     path="/api/w/products/search/imageQuery",
 *     summary="W 상품 이미지 조회",
 *     description="W 상품 이미지 조회 endPoint",
 *     tags={"상품"},
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="img_id",
 *         in="query",
 *         required=true,
 *         description="이미지 ID",
 *         @OA\Schema(
 *             type="string",
 *             example="1244408133008209457"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="begin_page",
 *         in="query",
 *         required=true,
 *         description="페이지 수",
 *         @OA\Schema(
 *             type="integer",
 *             example=1
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="page_size",
 *         in="query",
 *         required=true,
 *         description="페이징 사이즈 Max: 50",
 *         @OA\Schema(
 *             type="integer",
 *             example=50
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="sort",
 *         in="query",
 *         required=true,
 *         description="정렬조건 페이지마다 정렬임(전체 정렬X) | monthSold: 판매량, price: 가격",
 *         @OA\Schema(
 *             type="string",
 *             example="monthSold|desc"
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


class GWSearchImageQueryAnnotation{
}
