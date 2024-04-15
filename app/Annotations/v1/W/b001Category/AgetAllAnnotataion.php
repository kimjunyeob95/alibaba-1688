<?php

namespace App\Annotations\v1\W\b001Category;

/**
 * 
 * 
 *
 * @OA\Get(
 *     path="/api/w/category",
 *     summary="W 모든 카테고리",
 *     description="W 모든 카테고리 endPoint",
 *     tags={"카테고리"},
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="parent_cate_id",
 *         in="query",
 *         required=false,
 *         description="최상위 카테고리 ID",
 *         @OA\Schema(
 *             type="integer",
 *             example=0
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


class AgetAllAnnotataion{
}
