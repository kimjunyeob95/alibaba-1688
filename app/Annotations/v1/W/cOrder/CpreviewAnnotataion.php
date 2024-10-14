<?php

namespace App\Annotations\v1\W\cOrder;

/**
 * 
 * 
 * @OA\Schema(
 *     schema="OrderPreviewSchema",
 *     required={"offer_id", "option_param_list"},
 *     @OA\Property(
 *         property="offer_id",
 *         type="integer",
 *         example=730855260845,
 *         description="제품 ID"
 *     ),
 *     @OA\Property(
 *         property="option_param_list",
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
 *                 "option_id": 3479928,
 *                 "quantity": 1
 *             },
 *             {
 *                 "option_id": 3479929,
 *                 "quantity": 2
 *             }
 *         },
 *         description="주문 미리보기 제품 정보"
 *     )
 * )
 * 
 *
 * @OA\Post(
 *     path="/api/mall/{channel}/order/preview",
 *     @OA\Parameter(
 *         name="channel",
 *         in="path",
 *         required=true,
 *         description="채널 ID",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     summary="주문 미리보기",
 *     description="주문 미리보기 endPoint",
 *     tags={"주문"},
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/OrderPreviewSchema"),
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


class CpreviewAnnotataion{
}
