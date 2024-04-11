<?php

namespace App\Annotations\v1\W\b001Category;

/**
 * 
 * 
 *
 * @OA\Get(
 *     path="/api/w/category/mapping/{channel}",
 *     summary="W 채널별 카테고리 맵핑",
 *     description="W 채널별 카테고리 맵핑 조회 endPoint",
 *     tags={"카테고리"},
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="channel",
 *         in="path",
 *         required=true,
 *         description="채널 ID",
 *         @OA\Schema(
 *             type="string",
 *             example="onchannel"
 *         )
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


class CgetChannelAnnotataion{
}
