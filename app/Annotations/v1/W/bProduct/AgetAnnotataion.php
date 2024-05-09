<?php

namespace App\Annotations\v1\W\bProduct;

/**
 * @OA\Schema(
 *     schema="ProductListSuccessResponse",
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
 *         @OA\Property(
 *            property="result",
 *            type="array",
 *            @OA\Items(
 *               type="object",
 *               @OA\Property(
 *                   property="offer_id",
 *                   type="integer",
 *                   description="제품ID",
 *                   example=769405131194
 *               ),
 *               @OA\Property(
 *                   property="prd_name",
 *                   type="string",
 *                   description="제품명 원본",
 *                   example="网红ins欧式水洗棉复古牛仔外套女2024春秋韩版宽松短款长袖衬衫"
 *               ),
 *               @OA\Property(
 *                   property="prd_name_kr",
 *                   type="string",
 *                   description="제품명 번역",
 *                   example="인터넷 유명 인사 유럽 스타일 씻은 면화 복고풍 데님 코트 여성의 2024 봄과 가을 한국 스타일 느슨한 짧은 긴 소매 셔츠"
 *               ),
 *               @OA\Property(
 *                   property="options",
 *                   type="array",
 *                   @OA\Items(
 *                      type="object",
 *                      @OA\Property(
 *                          property="id",
 *                          type="integer",
 *                          description="옵션ID",
 *                          example=3498
 *                      ),
 *                      @OA\Property(
 *                          property="option_name",
 *                          type="string",
 *                          description="옵션명 원본",
 *                          example="牛仔蓝_S 建议96斤内"
 *                      ),
 *                      @OA\Property(
 *                          property="option_name_kr",
 *                          type="string",
 *                          description="옵션명 번역",
 *                          example="데님 블루_S 96 진 내에서 추천"
 *                      )
 *                  )
 *               ),
 *               @OA\Property(
 *                   property="images",
 *                   type="array",
 *                   @OA\Items(
 *                      type="object",
 *                      @OA\Property(
 *                          property="id",
 *                          type="integer",
 *                          description="이미지ID",
 *                          example=2647
 *                      ),
 *                      @OA\Property(
 *                          property="img_type",
 *                          type="string",
 *                          description="이미지 타입) main: 메인, sub: 서브, desc: 상세",
 *                          example="main"
 *                      ),
 *                      @OA\Property(
 *                          property="img_url_origin",
 *                          type="string",
 *                          description="이미지 원본 url",
 *                          example="https://cbu01.alicdn.com/img/ibank/O1CN01AWbFYv2CKRfBHrvTL_!!2216320858455-0-cib.jpg"
 *                      ),
 *                      @OA\Property(
 *                          property="img_url_trans",
 *                          type="string",
 *                          description="이미지 번역 url",
 *                          example="https://onch-1688.s3.ap-northeast-2.amazonaws.com/2024/04/09/769405131194_main.jpg"
 *                      )
 *                  )
 *               )
 *            )
 *         ),
 *         @OA\Property(
 *            property="lastPage",
 *            type="integer",
 *            description="마지막 페이지",
 *            example=18
 *         ),
 *         @OA\Property(
 *            property="page",
 *            type="integer",
 *            description="현재 페이지",
 *            example=1
 *         ),
 *         @OA\Property(
 *            property="pageSize",
 *            type="integer",
 *            description="페이지 크기",
 *            example=50
 *         ),
 *     )
 * )
 * 
 * 
 *
 * @OA\Get(
 *     path="/api/w/products",
 *     summary="상품 조회",
 *     description="W 상품 조회 endPoint",
 *     tags={"상품"},
 *     security={{"BearerAuth": {}}},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=true,
 *         description="페이지 수",
 *         @OA\Schema(
 *             type="integer",
 *             example=1
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="pageSize",
 *         in="query",
 *         required=true,
 *         description="페이지 크기 max 50",
 *         @OA\Schema(
 *             type="integer",
 *             example=50
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="trans_status",
 *         in="query",
 *         required=true,
 *         description="번역 여부) Y: 번역완료, N: 미번역",
 *         @OA\Schema(
 *             type="string",
 *             example="Y"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="search_cls",
 *         in="query",
 *         required=false,
 *         description="검색 타입) prd_name_kr: 상품명, option_name_kr: 옵션명",
 *         @OA\Schema(
 *             type="string",
 *             example="prd_name_kr"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="keyword",
 *         in="query",
 *         required=false,
 *         description="검색어",
 *         @OA\Schema(
 *             type="string",
 *             example="데님 블루"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/ProductListSuccessResponse")
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


class AgetAnnotataion{
}
