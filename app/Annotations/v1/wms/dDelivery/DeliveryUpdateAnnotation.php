<?php

namespace App\Annotations\v1\wms\dDelivery;

/**
 * 
 * @OA\Schema(
 *     schema="DeliveryUpdateSchema",
 *     required={"type", "group_no", "sh_nos"},
 *     @OA\Property(property="type", type="string", example="GR001", description="코드 타입 GR001: 배송 상태의 변경, GR002: 배송 정보의 변경"),
 *     @OA\Property(property="group_no", type="string", example="GR241015000381", description="그룹번호"),
 *     @OA\Property(
 *         property="sh_nos",
 *         type="array",
 *         description="신청서 번호 리스트",
 *         @OA\Items(type="string"),
 *         example={"SH241015000381", "SH241015000382"}
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/w/wms/request/bonaera/delivery/update",
 *     summary="배송 관련 정보의 변경",
 *     tags={"배송"},
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/DeliveryUpdateSchema")
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


class DeliveryUpdateAnnotation{
}
