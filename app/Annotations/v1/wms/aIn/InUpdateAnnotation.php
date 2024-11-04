<?php

namespace App\Annotations\v1\wms\aIn;

/**
 * 
 * @OA\Schema(
 *     schema="inUpdateSchema",
 *     required={"type", "stock_code"},
 *     @OA\Property(property="type", type="string", example="IT001"),
 *     @OA\Property(property="stock_code", type="string", example="ST241014000198"),
 * )
 *
 * @OA\Post(
 *     path="/api/w/wms/request/bonaera/in/update",
 *     summary="입고 관련 정보의 변경",
 *     tags={"입고"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/inUpdateSchema")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/SuccessResponse")
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


class InUpdateAnnotation{
}
