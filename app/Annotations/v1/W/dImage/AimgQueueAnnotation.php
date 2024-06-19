<?php

namespace App\Annotations\v1\W\dImage;

/**
 *
 * @OA\Schema(
 *     schema="ImageQueueSchema",
 *     required={"channel_queue_id", "member_id", "images"},
 *     @OA\Property(
 *         property="channel_queue_id",
 *         type="integer",
 *         example=1,
 *         description="채널 Queue ID"
 *     ),
 *     @OA\Property(
 *         property="member_id",
 *         type="string",
 *         example="tester123",
 *         description="판매자 계정 이름"
 *     ),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"id", "offer_id", "img_id","origin_url", "img_type"},
 *             @OA\Property(
 *                  property="id",
 *                  type="integer",
 *                  example=1,
 *                  description="채널에서 관리하는 이미지 고유 ID"
 *              ),
 *             @OA\Property(
 *                  property="offer_id",
 *                  type="integer",
 *                  example=679551697587,
 *                  description="1688 상품아이디"
 *              ),
 *             @OA\Property(
 *                  property="img_id",
 *                  type="integer",
 *                  example=1335888,
 *                  description="1688 상품이미지 아이디"
 *              ),
 *              @OA\Property(
 *                  property="origin_url",
 *                  type="string",
 *                  example="https://cbu01-overseas.1688.com/img/ibank/O1CN01AD7ffR26MNdlfhHsv_!!2201111757647-0-cib.jpg",
 *                  description="번역 이미지 url"
 *              ),
 *              @OA\Property(
 *                  property="img_type",
 *                  type="string",
 *                  example="main",
 *                  description="이미지 타입 main: 메인이미지 , sub: 서브이미지, desc: 상세이미지"
 *              ),
 *         ),
 *         description="이미지 정보"
 *     )
 * )
 *
 *
 * @OA\Post(
 *     path="/api/mall/{channel}/genuio/img/trans/request",
 *     @OA\Parameter(
 *         name="channel",
 *         in="path",
 *         required=true,
 *         description="채널 ID",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     summary="이미지 번역 요청 queue 등록",
 *     description="이미지 번역 요청 queue 등록 endPoint",
 *     tags={"이미지"},
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ImageQueueSchema"),
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


class AimgQueueAnnotation{
}
