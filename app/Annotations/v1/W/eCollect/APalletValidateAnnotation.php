<?php

namespace App\Annotations\v1\W\eCollect;

/**
 * @OA\Schema(
 *     schema="PalletValidationSuccessResponse",
 *     @OA\Property(property="status", type="integer", example=200),
 *     @OA\Property(
 *         property="meta", 
 *         type="object",
 *         @OA\Property(property="timestamp", type="string", example="2023-12-19 17:45:50"),
 *         @OA\Property(property="api_type", type="string", example="w")
 *     ),
 *     @OA\Property(
 *         property="data", 
 *         type="object",
 *         required={"validate"},
 *         @OA\Property(
 *             property="validate",
 *             type="boolean",
 *             description="성공 여부",
 *             example=true
 *         )
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/api/w/collect/pallet/validation/{pallet_id}",
 *     summary="WApp 팔레트 수집 여부 조회",
 *     description="WApp 팔레트 수집 여부 조회 endPoint",
 *     tags={"수집"},
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="pallet_id",
 *         in="path",
 *         required=true,
 *         description="팔레트ID",
 *         @OA\Schema(
 *             type="integer",
 *             example=215858073
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/PalletValidationSuccessResponse")
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


class APalletValidateAnnotation{
}
