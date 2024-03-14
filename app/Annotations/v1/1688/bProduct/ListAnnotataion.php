<?php

namespace App\Annotations\v1\genuio\bProduct;

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
 *     schema="ProductListSchema",
 *     required={"page", "pageSize", "orderBy", "direction"},
 *     @OA\Property(property="page", type="integer", example=1),
 *     @OA\Property(property="pageSize", type="string", example=40),
 *     @OA\Property(property="orderBy", type="string", example="prd_name"),
 *     @OA\Property(property="direction", type="string", example="desc")
 * )
 *
 * @OA\Post(
 *     path="/api/v1/product/list",
 *     summary="상품 리스트",
 *     tags={"상품"},
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ProductListSchema")
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


class ListAnnotataion{
}
