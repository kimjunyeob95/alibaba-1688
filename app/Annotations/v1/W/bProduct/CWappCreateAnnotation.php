<?php

namespace App\Annotations\v1\W\bProduct;

/**
 * 
 * 
 * @OA\Schema(
 *     schema="WappProductCreateSchema",
 *     required={"channel_type"},
 *     @OA\Property(
 *         property="channel_type",
 *         type="string",
 *         example="30",
 *         description="채널별 상품 전송 타입 30: OC 일반상품, 28: OC 사입상품, W: ES 셀러허브, DropHub: ES 1688 Official"
 *     )
 * )
 * 
 *
 * @OA\Post(
 *     path="/api/mall/{channel}/product/wapp/regist/{offerId}",
 *     @OA\Parameter(
 *         name="channel",
 *         in="path",
 *         required=true,
 *         description="채널 ID",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="offerId",
 *         in="path",
 *         required=true,
 *         description="제품 ID",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     summary="WApp 상품 생성 후 채널 전송",
 *     description="WApp 상품 생성 후 채널 전송 endPoint",
 *     tags={"상품"},
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/WappProductCreateSchema"),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/SuccessResponse"),
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


class CWappCreateAnnotation{
}
