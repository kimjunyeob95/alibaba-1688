<?php

namespace App\Annotations\v1\wms\bIn;

/**
 * 
 * @OA\Schema(
 *     schema="InUpdateSchema",
 *     required={"type", "stock_no"},
 *     @OA\Property(property="type", type="string", example="IT001", description="코드 타입 IT001: 입고 상태의 변경, IT002: 입고 정보 변경"),
 *     @OA\Property(property="stock_no", type="string", example="ST241014000198", description="재고번호"),
 * )
 *
 * @OA\Post(
 *     path="/api/w/wms/request/bonaera/in/update",
 *     summary="입고 관련 정보의 변경",
 *     tags={"입고"},
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/InUpdateSchema")
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
