<?php

namespace App\Annotations\v1\W\b001Category;

/**
 * 
 * 
 *
 * @OA\Get(
 *     path="/api/w/category/topList/{categoryId}",
 *     summary="W 카테고리별 인기상품 리스트",
 *     description="W 카테고리별 인기상품 리스트 endPoint",
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


class DTopListAnnotataion{
}
