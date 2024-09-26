<?php

namespace App\Annotations\v1\W\gWms;

/**
 * 
 * @OA\Get(
 *     path="/api/mall/{channel}/wms/hscode",
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
 *     summary="HS code 리스트 조회",
 *     description="HS code 리스트 조회",
 *     tags={"WMS"},
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="begin_page",
 *         in="query",
 *         required=true,
 *         description="페이지 수",
 *         @OA\Schema(
 *             type="integer",
 *             example=1
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="page_size",
 *         in="query",
 *         required=true,
 *         description="페이지 사이즈 Max 500",
 *         @OA\Schema(
 *             type="integer",
 *             example=50
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="search_cls",
 *         in="query",
 *         required=false,
 *         description="검색 분류 ko: 한글명, en: 영문명, hscode: HS code",
 *         @OA\Schema(
 *             type="string",
 *             example="ko"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="keyword",
 *         in="query",
 *         required=false,
 *         description="검색어",
 *         @OA\Schema(
 *             type="string",
 *             example=""
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


class AgetHsCodeListAnnotataion{
}
