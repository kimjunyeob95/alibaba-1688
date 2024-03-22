<?php

namespace App\Services\Product;

use App\Abstracts\OpenApiAbstract;
use App\Abstracts\ProductAbstract;
use App\Constants\Constant1688;
use App\Constants\ImageConstant;
use App\Constants\ProductConstant;
use App\Constants\ProductErrorMessageConstant;
use App\Models\CategoryMapping;
use App\Models\ProductData;
use App\Models\ProductExtendData;
use App\Models\ProductImageData;
use App\Models\ProductImageDetailData;
use App\Models\ProductNoticeData;
use App\Models\ProductOptionData;
use App\Vo\Product\Product1688Dto;
use App\Vo\Product\Product1688ExtendDto;
use App\Vo\Product\Product1688ImageDto;
use App\Vo\Product\Product1688NoticeDto;
use App\Vo\Product\Product1688OptionDto;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Psr\Log\LogLevel;
use UnexpectedValueException;

class ProductV1 extends ProductAbstract
{
    private array $returnMsg;
    private string $accessToken;
    private OpenApiAbstract $openApiAbstract;

    public function __construct(OpenApiAbstract $openApiAbstract)
    {
        $this->returnMsg       = helpers_fail_message();
        $this->accessToken     = env("1688_ACCESS_TOKEN");
        $this->openApiAbstract = $openApiAbstract;
    }

    public function getPrdList(array $params): LengthAwarePaginator
    {
        $pageSize  = $params["pageSize"];

        $prdBuilder = ProductData::with(["main_img", "options"])->orderBy("created_at", "desc");
        $lists = $prdBuilder->paginate($pageSize)->appends($params);

        return $lists;
    }

    public function getPrdDetail(int $offerId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $prdObj = ProductData::with([
                "images",
                "extends",
                "options",
                "notices",
                "category"
            ])->where("offer_id", $offerId)->first();
            if( $prdObj == null ){
                throw new Exception("No Data");   
            }

            $returnMsg = helpers_success_message($prdObj);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func getProductData
     * @description '1688 상품상세 endPoint 조회'
     * @param int $offerId '제품ID'
     */
    public function getProductData(int $offerId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
            $payload = [
                'access_token'     => $this->accessToken,
                'offerDetailParam' => [
                    'country' => Constant1688::LANGUAGE_KO,
                    'offerId' => $offerId,
                ]
            ];
            $returnMsg = curl_1688("post", $endPoint, $payload);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }
        return $returnMsg;
    }

    /**
     * @func saveMallProductByCategotyId
     * @description '1688 카테고리ID별 상품수집'
     */
    public function saveMallProductByCategotyId(int $categoryId): void
    {
        $msg = "======================== 실행 시작 (categoryId: {$categoryId}) ========================";
        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId");

        $page     = 1;
        $pageSize = 50;
        try {
            $this->saveMallProductRecursively($categoryId, $page, $pageSize);
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 (categoryId: {$categoryId}) ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId", LogLevel::ERROR);
        }

        $msg = "======================== 실행 종료 (categoryId: {$categoryId}) ========================";
        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId");
    }

    public function saveMallProductRecursively(int $categoryId, int $page, int $pageSize, int $totalPage = 0): void
    {
        $msg = "start saveMallProductRecursively | page: {$page} | categoryId: {$categoryId}";
        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId");

        $errorMsg = ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_KEYWORDQUERY") . " | categoryId: {$categoryId} | page: {$page}";
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.keywordQuery/";
            $payload = [
                'access_token'    => $this->accessToken,
                'offerQueryParam' => [
                    'keyword'    => '',
                    'beginPage'  => $page,
                    'pageSize'   => $pageSize,
                    'country'    => Constant1688::LANGUAGE_KO,
                    'categoryId' => $categoryId,
                ]
            ];
            $apiDatas = curl_1688("POST", $endPoint, $payload);
            if( $apiDatas["isSuccess"] != true ){
                throw new Exception($apiDatas["msg"] . " | " . $errorMsg);
            }
            if( $apiDatas["data"]["result"]["success"] != true ){
                throw new Exception($errorMsg);
            }

            $apiResult = $apiDatas["data"]["result"]["result"];
            if( isset($apiResult["data"]) ){
                $productDatas = $apiResult["data"];
                $successCnt   = 0;
                foreach ($productDatas as $productData) {
                    try {
                        $offerId        = $productData["offerId"];
                        $endPoint       = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                        $payload        = [
                            'access_token'     => $this->accessToken,
                            'offerDetailParam' => [
                                'offerId' => $offerId,
                                'country' => Constant1688::LANGUAGE_KO,
                            ]
                        ];
                        $detailResult = curl_1688("POST", $endPoint, $payload);
                        if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                            throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                        }

                        $detailProduct = $detailResult["data"]["result"]["result"];
                        $prdCategoryId = $detailProduct["categoryId"];

                        $getCategoryMappingObj = CategoryMapping::select(["mapping_code"])
                        ->where("category_id", $prdCategoryId)
                        ->where("mapping_channel", ProductConstant::MAPPING_OC_CHANNEL)
                        ->where("mapping_code", "!=", 0)->first();
                        if( $getCategoryMappingObj == null ){
                            continue;
                        }

                        $trans_status = "Y"; // 상품 번역 완료 여부

                        // 1. 상품 이미지
                        $product1688ImageDtoList = [];
                        foreach ($detailProduct["productImage"]["images"] as $imgKey => $prdImage) {
                            if( $imgKey == 0 ) {
                                $imgType = ImageConstant::IMAGE_TYPE_MAIN;
                            } else {
                                $imgType = ImageConstant::IMAGE_TYPE_SUB;
                            }
                            $isChangeImg = $this->isChangeImage($offerId, $prdImage, $imgType);
                            $imgWidth    = 0;
                            $imgHeight   = 0;
                            $imgByte     = 0;
                            $imgMime     = "";
                            if( $isChangeImg == true ){
                                $imageInfo = $this->checkImageSize($prdImage);
                                $imgWidth  = $imageInfo["width"];
                                $imgHeight = $imageInfo["height"];
                                $imgByte   = $imageInfo["byte"];
                                $imgMime   = $imageInfo["mime"];

                                $trans_status = "N";
                            }
                            $product1688ImageDto = new Product1688ImageDto();
                            $product1688ImageDto->bind([
                                "offerId"        => $offerId,
                                "imgType"        => $imgType,
                                "img_url_origin" => $prdImage,
                                "img_url_trans"  => "",
                                "isChangeImg"    => $isChangeImg,
                                "width"          => $imgWidth,
                                "height"         => $imgHeight,
                                "byte"           => $imgByte,
                                "mime"           => $imgMime
                            ]);
                            $product1688ImageDtoList[] = $product1688ImageDto;
                        }

                        // 1-1. 상품 상세 이미지
                        $prdDescription = $detailProduct["description"];
                        preg_match_all('/<img[^>]+src="([^">]+)"/', $prdDescription, $matches);
                        $imageSrcs = $matches[1];
                        foreach ($imageSrcs as $imageSrc) {
                            $imgType     = ImageConstant::IMAGE_TYPE_DESC;
                            $isChangeImg = $this->isChangeImage($offerId, $imageSrc, $imgType);
                            $imgWidth    = 0;
                            $imgHeight   = 0;
                            $imgByte     = 0;
                            $imgMime     = "";
                            if( $isChangeImg == true ){
                                $imageInfo = $this->checkImageSize($imageSrc);
                                $imgWidth  = $imageInfo["width"];
                                $imgHeight = $imageInfo["height"];
                                $imgByte   = $imageInfo["byte"];
                                $imgMime   = $imageInfo["mime"];

                                $trans_status = "N";
                            }
                            $product1688ImageDto = new Product1688ImageDto();
                            $product1688ImageDto->bind([
                                "offerId"        => $offerId,
                                "imgType"        => $imgType,
                                "img_url_origin" => $imageSrc,
                                "img_url_trans"  => "",
                                "isChangeImg"    => $isChangeImg,
                                "width"          => $imgWidth,
                                "height"         => $imgHeight,
                                "byte"           => $imgByte,
                                "mime"           => $imgMime
                            ]);
                            $product1688ImageDtoList[] = $product1688ImageDto;
                        }

                        // 2. 상품 기본정보
                        $startQuantity = $detailProduct["productSaleInfo"]["priceRangeList"][0]["startQuantity"];
                        $product1688Dto = new Product1688Dto();
                        $product1688Dto->bind([
                            "offerId"       => $offerId,
                            "categoryId"    => $prdCategoryId,
                            "subject"       => $detailProduct["subject"],
                            "subjectTrans"  => $detailProduct["subjectTrans"],
                            "startQuantity" => $startQuantity,
                            "trans_status"  => $trans_status,
                            "description"   => $detailProduct["description"],
                            "response_json" => json_encode($detailProduct, JSON_UNESCAPED_UNICODE),
                        ]);

                        // 2. 상품 확장정보
                        $product1688ExtendDto = new Product1688ExtendDto();
                        $product1688ExtendDto->bind([
                            "offerId" => $offerId,
                        ]);

                        // 4. 상품 고시정보
                        $product1688NoticeDtoList = [];
                        foreach ($detailProduct["productAttribute"] as $prdNotice) {
                            $product1688NoticeDto = new Product1688NoticeDto();
                            $product1688NoticeDto->bind([
                                "offerId"            => $offerId,
                                "attributeId"        => $prdNotice["attributeId"],
                                "attributeName"      => $prdNotice["attributeName"],
                                "value"              => $prdNotice["value"],
                                "attributeNameTrans" => $prdNotice["attributeNameTrans"],
                                "valueTrans"         => $prdNotice["valueTrans"]
                            ]);
                            $product1688NoticeDtoList[] = $product1688NoticeDto;
                        }

                        // 5. 상품 옵션정보
                        $product1688OptionDtoList = [];

                        $price_1688 = 0;
                        // 5-1. price 컬럼이 있을 경우
                        if( isset($detailProduct["productSkuInfos"][0]["price"]) ){
                            foreach ($detailProduct["productSkuInfos"] as $prdOptions) {
                                if( $prdOptions["price"] > $price_1688 ){
                                    $price_1688 = $prdOptions["price"];
                                }
                            }
                        } else if( !isset($detailProduct["productSkuInfos"][0]["price"]) && 
                            isset($detailProduct["productSaleInfo"]["priceRangeList"])
                        ) {
                            $price_1688 = $detailProduct["productSaleInfo"]["priceRangeList"][0]["price"];
                        } 
                        
                        if( $price_1688 == 0 ){
                            throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRICE_1688"));
                        }

                        foreach ($detailProduct["productSkuInfos"] as $prdOptions) {
                            $optionName      = "";
                            $optionNameTrans = "";
                            foreach ($prdOptions["skuAttributes"] as $prdOption) {
                                $optionName      .= $prdOption["value"] .  "_";
                                $optionNameTrans .= $prdOption["valueTrans"] .  "_";
                            }
                            $product1688OptionDto = new Product1688OptionDto();
                            $product1688OptionDto->bind([
                                "offerId"         => $offerId,
                                "skuId"           => $prdOptions["skuId"],
                                "specId"          => $prdOptions["specId"],
                                "price_1688"      => $price_1688,
                                "optionName"      => rtrim($optionName, "_"),
                                "optionNameTrans" => rtrim($optionNameTrans, "_"),
                                "amountOnSale"    => $prdOptions["amountOnSale"],
                                "cargoNumber"     => $prdOptions["cargoNumber"] ?? "",
                            ]);
                            $product1688OptionDtoList[] = $product1688OptionDto;
                        }

                        $saveResult = $this->save1688ProductData($product1688Dto, $product1688ExtendDto, $product1688ImageDtoList, $product1688NoticeDtoList, $product1688OptionDtoList);

                        if( $saveResult["isSuccess"] == true ){
                            $successCnt++;
                        }else{
                            throw new Exception($saveResult["msg"]);
                        }
                    } catch (Exception $de) {
                        $msg = $de->getMessage() . " | page: {$page} | offerId: {$offerId} | categoryId: {$categoryId} | prdCategoryId: {$prdCategoryId}";
                        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId", LogLevel::ERROR);
                    } catch (UnexpectedValueException $ue) {
                        $msg = $ue->getMessage() . " | page: {$page} | offerId: {$offerId} | categoryId: {$categoryId} | prdCategoryId: {$prdCategoryId}";
                        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId", LogLevel::ERROR);
                    }
                }
            } else {
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT_SEARCH_KEYWORDQUERY") . " | page: {$page} | categoryId: {$categoryId}");
            }

            $totalPage = $apiDatas["data"]["result"]["result"]["totalPage"];
            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveMallProductRecursively($categoryId, $nextPage, $pageSize, $totalPage);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId", LogLevel::ERROR);

            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveMallProductRecursively($categoryId, $nextPage, $pageSize, $totalPage);
            }
        }
    }

    /**
     * @func save1688ProductData
     * @description '1688 상품 DB저장'
     */
    public function save1688ProductData(
        Product1688Dto $product1688Dto, Product1688ExtendDto $product1688ExtendDto, array $product1688ImageDtoList,
        array $product1688NoticeDtoList, array $product1688OptionDtoList): array
    {
        $returnMsg = helpers_fail_message();
        try {
            // 1. product_datas upsert
            $upsertWhere = $product1688Dto->getAllProperties();
            unset($upsertWhere["offer_id"]);
            ProductData::updateOrCreate(
                ["offer_id" => $product1688Dto->offer_id],
                $upsertWhere
            );

            // 2. product_extend_datas upsert
            $upsertWhere = $product1688ExtendDto->getAllProperties();
            unset($upsertWhere["offer_id"]);
            ProductExtendData::updateOrCreate(
                ["offer_id" => $product1688ExtendDto->offer_id],
                $upsertWhere
            );

            // 3. product_image_datas, product_image_detail_datas upsert
            foreach ($product1688ImageDtoList as $product1688ImageDto) {
                // 메인 이미지
                if( $product1688ImageDto->is_change_img == true && $product1688ImageDto->img_type == ImageConstant::IMAGE_TYPE_MAIN ){
                    ProductImageData::updateOrCreate(
                        [
                            "offer_id" => $product1688ImageDto->offer_id,
                            "img_type" => ImageConstant::IMAGE_TYPE_MAIN,
                        ],
                        [
                            "img_url_origin" => $product1688ImageDto->img_url_origin,
                            "img_url_trans"  => "",
                            "trans_dated_at" => null
                        ]
                    );
                }
                // 서브 이미지 or 상세 이미지
                if( $product1688ImageDto->is_change_img == true && $product1688ImageDto->img_type != ImageConstant::IMAGE_TYPE_MAIN ){
                    ProductImageData::updateOrCreate(
                        [
                            "offer_id"       => $product1688ImageDto->offer_id,
                            "img_type"       => $product1688ImageDto->img_type,
                            "img_url_origin" => $product1688ImageDto->img_url_origin,
                        ],
                        [
                            "img_url_trans" => "",
                            "trans_dated_at" => null
                        ]
                    );
                }

                $upsertDetailWhere = [];
                if( $product1688ImageDto->is_change_img == true ){
                    $upsertDetailWhere = [
                        "width"  => $product1688ImageDto->width,
                        "height" => $product1688ImageDto->height,
                        "byte"   => $product1688ImageDto->byte,
                        "mime"   => $product1688ImageDto->mime,
                    ];
                }
                if( !empty($upsertDetailWhere) ){
                    ProductImageDetailData::updateOrCreate(
                        [
                            "offer_id"       => $product1688ImageDto->offer_id,
                            "img_type"       => $product1688ImageDto->img_type,
                            "img_url_origin" => $product1688ImageDto->img_url_origin,
                        ],
                        $upsertDetailWhere
                    );
                }
            }

            // 4. product_notice_datas upsert
            foreach ($product1688NoticeDtoList as $product1688NoticeDto) {
                $upsertWhere = $product1688NoticeDto->getAllProperties();
                unset($upsertWhere["offer_id"]);
                unset($upsertWhere["attribute_id"]);
                ProductNoticeData::updateOrCreate(
                    [
                        "offer_id"     => $product1688NoticeDto->offer_id,
                        "attribute_id" => $product1688NoticeDto->attribute_id,
                    ],
                    $upsertWhere
                );
            }

            // 5. product_option_datas upsert
            foreach ($product1688OptionDtoList as $product1688OptionDto) {
                $upsertWhere = $product1688OptionDto->getAllProperties();
                unset($upsertWhere["offer_id"]);
                unset($upsertWhere["sku_id"]);
                unset($upsertWhere["spec_id"]);
                ProductOptionData::updateOrCreate(
                    [
                        "offer_id" => $product1688OptionDto->offer_id,
                        "sku_id"   => $product1688OptionDto->sku_id,
                        "spec_id"  => $product1688OptionDto->spec_id,
                    ],
                    $upsertWhere
                );
            }

            // 6. 이미지 번역 요청 통신 
            $this->openApiAbstract->createTransProductImg($product1688ImageDtoList, $product1688Dto->offerId);

            // 7. 기존 이미지 삭제
            $this->delProductImage($product1688ImageDtoList);

            // 8. 변역 완료 여부 체크
            $noTransCnt = ProductImageData::where("offer_id", $product1688Dto->offerId)
            ->where("img_url_trans", "")
            ->whereNull("trans_dated_at")
            ->count();
            ProductData::where("offer_id", $product1688Dto->offerId)->update([
                "trans_status" => $noTransCnt == 0 ? ProductConstant::TRANS_STATUE_Y : ProductConstant::TRANS_STATUE_N
            ]);

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }
        return $returnMsg;
    }

    /**
     * @func delProductImage
     * @description '불핑요 제품 이미지 삭제'
     * @param array $product1688ImageDtoList
     */
    public function delProductImage(array $product1688ImageDtoList): void
    {
        $mainImgs = [];
        $subImgs  = [];
        $descImgs = [];
        foreach ($product1688ImageDtoList as $product1688ImageDto) {
            if( $product1688ImageDto->img_type == ImageConstant::IMAGE_TYPE_MAIN ){
                $mainImgs[$product1688ImageDto->offer_id][] = $product1688ImageDto->img_url_origin;
            }
            if( $product1688ImageDto->img_type == ImageConstant::IMAGE_TYPE_SUB ){
                $subImgs[$product1688ImageDto->offer_id][] = $product1688ImageDto->img_url_origin;
            }
            if( $product1688ImageDto->img_type == ImageConstant::IMAGE_TYPE_DESC ){
                $descImgs[$product1688ImageDto->offer_id][] = $product1688ImageDto->img_url_origin;
            }
        }

        // 1. 메인 이미지 삭제
        foreach ($mainImgs as $offer_id => $mainImg) {
            ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_MAIN)
            ->where('offer_id', $offer_id)
            ->where('img_url_origin', '!=', $mainImg)
            ->delete();
        }

        // 2. 서브 이미지 삭제
        foreach ($subImgs as $offer_id => $subImg) {
            ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_SUB)
            ->where('offer_id', $offer_id)
            ->whereNotIn('img_url_origin', $subImg)
            ->delete();
        }

        // 3. 상세 이미지 삭제
        foreach ($descImgs as $offer_id => $descImg) {
            ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_DESC)
            ->where('offer_id', $offer_id)
            ->whereNotIn('img_url_origin', $descImg)
            ->delete();
        }
    }

    /**
     * @func isChangeImage
     * @description '이미지 변화 여부 검사 메소드'
     * @param int $offerId
     * @param string $imagePath
     * @param string $imgType
     */
    public function isChangeImage(int $offerId, string $imagePath, string $imgType): bool
    {
        $isChange = false;

        $getOriginImgObj = ProductImageDetailData::where("offer_id", $offerId)
        ->where("img_type", $imgType)
        ->where("img_url_origin", $imagePath)->first();

        if( $getOriginImgObj == null ){
            $isChange = true;
        } else {
            $imageInfo = $this->checkImageSize($imagePath);
            $imgWidth  = $imageInfo["width"];
            $imgHeight = $imageInfo["height"];
            $imgByte   = $imageInfo["byte"];
            $imgMime   = $imageInfo["mime"];

            if( $getOriginImgObj->width != $imgWidth || $getOriginImgObj->height != $imgHeight 
            && $getOriginImgObj->byte != $imgByte && $getOriginImgObj->mime != $imgMime){
                $isChange = true;
            }
        }

        return $isChange;
    }

    /**
     * @func checkImageSize
     * @description '이미지 사이즈 검사 메소드'
     * @param string $imagePath
     */
    public function checkImageSize(string $imagePath): array
    {
        try {
            $imageInfo = getimagesize($imagePath);
        } catch (Exception $e) {
            $errorMsg = ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_CHECK_IMG_SIZE") . " {$imagePath}" . " | error: " . $e->getMessage();
            throw new UnexpectedValueException($errorMsg);
        }

        return [
            "width"  => $imageInfo[0],
            "height" => $imageInfo[1],
            "byte"   => $imageInfo["bits"],
            "mime"   => $imageInfo["mime"],
        ];
    }
}