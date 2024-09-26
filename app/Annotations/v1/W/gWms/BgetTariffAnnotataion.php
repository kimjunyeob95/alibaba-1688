<?php

namespace App\Annotations\v1\W\gWms;

/**
 * 
 * @OA\Get(
 *     path="/api/mall/{channel}/wms/tariff/{hs_code}",
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
 *     @OA\Parameter(
 *         name="hs_code",
 *         in="path",
 *         required=true,
 *         description="HS code",
 *         @OA\Schema(
 *             type="string",
 *             example="0712391030"
 *         )
 *     ),
 *     summary="관세율 조회 조회",
 *     description="관세율 조회 조회",
 *     tags={"WMS"},
 *     security={{"BearerAuth": {}}},
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


class BgetTariffAnnotataion{
}
