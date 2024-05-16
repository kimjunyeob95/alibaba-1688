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
 *     @OA\Property(
 *         property="offer_id",
 *         type="integer",
 *         example=715819550080,
 *         description="제품 ID"
 *     ),
 *     @OA\Property(
 *         property="optionParamList",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"option_id", "quantity"},
 *             @OA\Property(
 *                  property="option_id",
 *                  type="integer",
 *                  description="옵션 ID"
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
 *                 "option_id": 217126,
 *                 "quantity": 1
 *             },
 *             {
 *                 "option_id": 217127,
 *                 "quantity": 2
 *             }
 *         },
 *         description="주문 생성 제품 정보"
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
 *         @OA\Property(property="apiType", type="string", example="mall")
 *     ),
 *     @OA\Property(
 *         property="data", 
 *         type="array",
 *         @OA\Items(
 *            type="object",
 *            required={"order_id", "isSuccess"},
 *            @OA\Property(
 *                property="order_id",
 *                type="integer",
 *                description="주문 ID",
 *                example=3890000700554135493
 *            ),
 *            @OA\Property(
 *                property="isSuccess",
 *                type="boolean",
 *                description="성공 여부",
 *                example=true
 *            )
 *         ),
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
