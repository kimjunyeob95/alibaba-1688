<?php

namespace App\Annotations\v1\W\fExchangeRate;

/**
 *
 * @OA\Get(
 *     path="/api/w/exchangeRate",
 *     summary="WApp 환율 조회",
 *     description="WApp 환율 조회 endPoint",
 *     tags={"환율"},
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

class AExchangeRateAnnotation{
}
