<?php

namespace App\Annotations\v1\W\bProduct;

/**
 *
 * @OA\Get(
 *     path="/api/w/products/{offerId}",
 *     summary="WApp 상품 상세",
 *     description="WApp 상품 상세 endPoint",
 *     tags={"상품"},
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="offerId",
 *         in="path",
 *         required=true,
 *         description="상품 ID",
 *         @OA\Schema(
 *             type="integer",
 *             example=730301197041
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


class AgetDetailAnnotataion{
}
