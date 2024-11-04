<?php

namespace App\Annotations\v1\wms\bOut;

/**
 * 
 * @OA\Schema(
 *     schema="OutUpdateSchema",
 *     required={"type", "group_no", "sh_nos"},
 *     @OA\Property(property="type", type="string", example="SH001", description="코드 타입"),
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
 *     path="/api/w/wms/request/bonaera/out/update",
 *     summary="출고 관련 정보의 변경",
 *     tags={"출고"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/OutUpdateSchema")
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


class OutUpdateAnnotation{
}
