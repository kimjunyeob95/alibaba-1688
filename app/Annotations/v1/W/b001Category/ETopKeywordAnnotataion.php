<?php

namespace App\Annotations\v1\W\b001Category;

/**
 * 
 * 
 *
 * @OA\Get(
 *     path="/api/w/category/topKeyword/{categoryId}",
 *     summary="W 카테고리별 인기검색어 조회",
 *     description="W 카테고리별 인기검색어 조회 endPoint",
 *     tags={"카테고리"},
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="categoryId",
 *         in="path",
 *         required=true,
 *         description="카테고리 ID",
 *         @OA\Schema(
 *             type="integer",
 *             example="10165"
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


class ETopKeywordAnnotataion{
}
