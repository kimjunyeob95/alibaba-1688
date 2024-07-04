<?php

namespace App\Annotations\v1\W\bProduct;

/**
 * 
 * @OA\Schema(
 *     schema="WSearchCreateImageIdByUrlSchema",
 *     required={"img_url"},
 *     @OA\Property(
 *         property="img_url",
 *         type="string",
 *         example="https://nhci-aigc.oss-cn-zhangjiakou.aliyuncs.com/ppc-records%2Fimage-remove%2Fd184c97f-6f3e-452b-8be2-36106f495637.png?OSSAccessKeyId=LTAI5tCv9DpB7gYic1oGsAyv&Expires=4924420085&Signature=cc8oiKbhGtUSe5aGuoVXidQTZRM%3D",
 *         description="이미지 URL"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/w/products/search/create/imageIdByUrl",
 *     summary="W 상품 이미지URL로 이미지 ID 생성",
 *     description="W 상품 이미지URL로 이미지 ID 생성 endPoint",
 *     tags={"상품"},
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/WSearchCreateImageIdByUrlSchema"),
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


class F2WSearchCreateImageIdByUrlAnnotation{
}
