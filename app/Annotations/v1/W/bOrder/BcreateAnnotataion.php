<?php

namespace App\Annotations\v1\W\bOrder;

/**
 * 
 * 
 * @OA\SecurityScheme(
 *      securityScheme="BearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT"
 * )
 * 
 * @OA\Schema(
 *     schema="OrderCreateSchema",
 *     required={"cargoParamList", "receive_name", "receive_tell", "receive_phone"},
 *     @OA\Property(
 *         property="cargoParamList",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"offer_id", "option_id"},
 *             @OA\Property(
 *                 property="offer_id",
 *                 type="integer",
 *                 example=630519988300,
 *                 description="제품 ID"
 *             ),
 *             @OA\Property(
 *                 property="option_id",
 *                 type="integer",
 *                 example=291,
 *                 description="제품 옵션 ID"
 *             ),
 *             @OA\Property(
 *                 property="quantity",
 *                 type="integer",
 *                 example="1",
 *                 description="제품 수량"
 *             ),
 *         ),
 *         description="주문 생성 제품 정보"
 *     ),
 *     @OA\Property(property="receive_name", type="string", description="수취인 이름", example="홍길동"),
 *     @OA\Property(property="receive_tell", type="string", description="수취인 전화번호", example="021231324"),
 *     @OA\Property(property="receive_phone", type="string", description="수취인 휴대폰번호", example="01012345678"),
 *     @OA\Property(property="message", type="string", description="주문 메모", example="문 앞에 놔주세요.")
 * )
 *
 * @OA\Post(
 *     path="/api/mall/{channel}/order/create",
 *     @OA\Parameter(
 *         name="channel",
 *         in="path",
 *         required=true,
 *         description="채널 ID",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     summary="주문 생성",
 *     description="주문 생성 endPoint",
 *     tags={"주문"},
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/OrderCreateSchema")
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


class BcreateAnnotataion{
}
