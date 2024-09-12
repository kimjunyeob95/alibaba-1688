<?php

namespace App\Annotations\v1\W\cOrder;

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
 *     required={"offer_id", "channel_order_id", "option_param_list", "delivery_price", "buyer_name", "buyer_clearance_number", "buyer_number", "buyer_phone", "buyer_zipcode", "buyer_address", "buyer_memo"},
 *     @OA\Property(
 *         property="offer_id",
 *         type="integer",
 *         example=730855260845,
 *         description="제품 ID"
 *     ),
 *     @OA\Property(
 *         property="clearance_type",
 *         type="string",
 *         example="SA",
 *         description="통관유형 SA: 개인통관, PA: 사업자통관(사업자 명의), IA: 사업자통관(온채널 또는 셀러허브 명의)"
 *     ),
 *     @OA\Property(
 *         property="shipping_type",
 *         type="string",
 *         example="OF",
 *         description="운송방식 OF: 해운, AF: 항공"
 *     ),
 *     @OA\Property(
 *         property="channel_order_id",
 *         type="string",
 *         example="test2016179999",
 *         description="채널 주문 번호"
 *     ),
 *     @OA\Property(
 *         property="option_param_list",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"option_id", "price", "quantity"},
 *             @OA\Property(
 *                  property="option_id",
 *                  type="integer",
 *                  description="옵션 ID"
 *              ),
 *             @OA\Property(
 *                  property="price",
 *                  type="float",
 *                  description="채널에 등록 된 제품 가격(WAPP에서 전송한 가격: 원화)"
 *              ),
 *              @OA\Property(
 *                  property="quantity",
 *                  type="integer",
 *                  description="구매 수량"
 *              ),
 *         ),
 *         example=
 *         {
 *             {
 *                 "option_id": 3479928,
 *                 "price": 1300,
 *                 "quantity": 1
 *             },
 *             {
 *                 "option_id": 3479929,
 *                 "price": 1200,
 *                 "quantity": 2
 *             }
 *         },
 *         description="주문 생성 제품 정보"
 *     ),
 *     @OA\Property(
 *         property="delivery_price",
 *         example="3000",
 *         description="배송비(원화)"
 *     ),
 *     @OA\Property(
 *         property="buyer_name",
 *         example="홍길동",
 *         description="구매자명"
 *     ),
 *     @OA\Property(
 *         property="buyer_clearance_number",
 *         type="string",
 *         example="1234567891011",
 *         description="구매자 개인통관번호"
 *     ),
 *     @OA\Property(
 *         property="buyer_number",
 *         type="string",
 *         example="01012345678",
 *         description="구매자 전화번호"
 *     ),
 *     @OA\Property(
 *         property="buyer_phone",
 *         type="string",
 *         example="01012345678",
 *         description="구매자 핸드폰번호"
 *     ),
 *     @OA\Property(
 *         property="buyer_zipcode",
 *         type="string",
 *         example="12345",
 *         description="구매자 우편번호"
 *     ),
 *     @OA\Property(
 *         property="buyer_address",
 *         type="string",
 *         example="서울특별시 강남구 삼성동 144-5 401호",
 *         description="구매자 주소"
 *     ),
 *     @OA\Property(
 *         property="buyer_memo",
 *         type="string",
 *         example="문 앞에 놓아주세요.",
 *         description="배송 요청사항"
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="OrderCreateSuccessResponse",
 *     @OA\Property(property="status", type="integer", example=200),
 *     @OA\Property(
 *         property="meta", 
 *         type="object",
 *         @OA\Property(property="timestamp", type="string", example="2023-12-19 17:45:50"),
 *         @OA\Property(property="api_type", type="string", example="mall")
 *     ),
 *     @OA\Property(
 *         property="data", 
 *         type="object",
 *         required={"order_id", "channel_order_id", "success"},
 *         @OA\Property(
 *             property="order_id",
 *             type="string",
 *             description="주문 ID",
 *             example="3890000700554135493"
 *         ),
 *         @OA\Property(
 *             property="channel_order_id",
 *             type="string",
 *             description="채널 주문 번호",
 *             example="test2016179999"
 *         ),
 *         @OA\Property(
 *             property="success",
 *             type="boolean",
 *             description="성공 여부",
 *             example=true
 *         )
 *      )
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
 *         @OA\JsonContent(ref="#/components/schemas/OrderCreateSchema"),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/OrderCreateSuccessResponse"),
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
