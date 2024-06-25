<?php

namespace App\Annotations\v1\W\eCollect;

/**
 * 
 * @OA\Get(
 *     path="/api/w/collect/pallet/{pallet_id}",
 *     summary="WApp 팔레트 수집 조회",
 *     description="WApp 팔레트 수집 조회 endPoint",
 *     tags={"수집"},
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="pallet_id",
 *         in="path",
 *         required=true,
 *         description="팔레트ID",
 *         @OA\Schema(
 *             type="integer",
 *             example=1
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


class APalletListAnnotation{
}
