<?php

namespace App\Annotations\v1\wms\dDelivery;

/**
 * 
 * @OA\Schema(
 *     schema="DeliveryBundleSchema",
 *     required={"type", "sh_no", "origin_group_no", "change_group_no"},
 *     @OA\Property(property="type", type="string", example="GR003", description="코드 타입 GR003: 묶음 배송변경"),
 *     @OA\Property(property="sh_no", type="string", example="SH241114001737", description="출고 번호"),
 *     @OA\Property(property="origin_group_no", type="string", example="GR241114001730", description="원본 배송 번호"),
 *     @OA\Property(property="change_group_no", type="string", example="GR241114001731", description="변경 배송 번호")
 * )
 *
 * @OA\Post(
 *     path="/api/w/wms/request/bonaera/delivery/bundle",
 *     summary="묶음 배송 처리",
 *     tags={"배송"},
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/DeliveryBundleSchema")
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

class DeliveryBundleAnnotation{
}
