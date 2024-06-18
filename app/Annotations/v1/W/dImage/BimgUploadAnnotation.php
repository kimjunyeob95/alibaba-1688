<?php

namespace App\Annotations\v1\W\dImage;

/**
 * 
 * @OA\Schema(
 *     schema="ImageUploadSchema",
 *     required={"member_id", "images"},
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
 *             required={"id", "base64"},
 *             @OA\Property(
 *                  property="id",
 *                  type="integer",
 *                  example=1,
 *                  description="채널에서 관리하는 이미지 고유 ID"
 *              ),
 *              @OA\Property(
 *                  property="base64",
 *                  type="string",
 *                  example="~~~",
 *                  description="이미지 base64 인코딩"
 *              ),
 *              @OA\Property(
 *                  property="cleaned_base64",
 *                  type="string",
 *                  example="~~~",
 *                  description="흰 배경 이미지 base64 인코딩"
 *              ),
 *         ),
 *         description="이미지 정보"
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="ImageUploadSuccessResponse",
 *     @OA\Property(property="status", type="integer", example=200),
 *     @OA\Property(
 *         property="meta", 
 *         type="object",
 *         @OA\Property(property="timestamp", type="string", example="2023-12-19 17:45:50"),
 *         @OA\Property(property="apiType", type="string", example="mall")
 *     ),
 *     @OA\Property(
 *         property="data", 
 *         type="object",
 *         required={"images"},
 *         @OA\Property(
 *             property="images",
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 required={"id", "trans_url", "upload", "error"},
 *                 @OA\Property(
 *                      property="id",
 *                      type="integer",
 *                      example=1,
 *                      description="채널에서 관리하는 이미지 고유 ID"
 *                  ),
 *                 @OA\Property(
 *                      property="trans_url",
 *                      type="string",
 *                      example="https://namver.png",
 *                      description="S3 upload img url"
 *                  ),
 *                  @OA\Property(
 *                      property="upload",
 *                      type="boolean",
 *                      example=true,
 *                      description="업로드 성공 여부"
 *                  ),
 *                  @OA\Property(
 *                      property="error",
 *                      type="string",
 *                      example="",
 *                      description="에러 메세지"
 *                  )
 *              ),
 *             description="이미지 결과 Response"
 *         )
 *      )
 * )
 * 
 *
 * @OA\Post(
 *     path="/api/mall/{channel}/img/upload",
 *     @OA\Parameter(
 *         name="channel",
 *         in="path",
 *         required=true,
 *         description="채널 ID",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     summary="이미지 S3 업로드",
 *     description="이미지 S3 업로드 endPoint",
 *     tags={"이미지"},
 *     security={{"BearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ImageUploadSchema"),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/ImageUploadSuccessResponse"),
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


class BimgUploadAnnotation{
}
