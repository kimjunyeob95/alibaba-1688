<?php

namespace App\Services;

use App\Abstracts\ApiModuleAbstract;
use App\Abstracts\OpenApiAbstract;
use App\Abstracts\UploadAbstract;
use App\Constants\CategoryErrorMessageConstant;
use App\Constants\ImageConstant;
use App\Constants\ProductConstant;
use App\Constants\ProductErrorMessageConstant;
use App\Models\Category;
use App\Models\CategoryMapping;
use App\Models\CategoryTree;
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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use JsonException;
use Psr\Log\LogLevel;
use UnexpectedValueException;

class Service1688 extends ApiModuleAbstract
{
    private string $appKey;
    private string $appSecret;
    private string $accessToken;
    private string $appEnv;
    private UploadAbstract $uploadAbstract;
    private OpenApiAbstract $openApiAbstract;

    public function __construct(UploadAbstract $uploadAbstract, OpenApiAbstract $openApiAbstract)
    {
        parent::__construct(env("1688_API_DOMAIN", "https://gw.open.1688.com/openapi/"));

        $this->appKey          = env("1688_APP_KEY");
        $this->appSecret       = env("1688_APP_SECRET_KEY");
        $this->accessToken     = env("1688_ACCESS_TOKEN");
        $this->appEnv          = ( env("APP_ENV", "local") != "production" ) ? "dev/" : "";
        $this->uploadAbstract  = $uploadAbstract;
        $this->openApiAbstract = $openApiAbstract;
    }
    
    /**
     * @func getAllCategory
     * @description '1688에서 수집 한 카테고리를 단계별로 정리한 데이터 목록'
     */
    public function getAllCategory(): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $getCategoryTreeObjs = CategoryTree::orderBy("cate_first", "asc")->orderBy("cate_second", "asc")->orderBy("cate_third", "asc")->get();
            $result = [
                "total" => count($getCategoryTreeObjs),
            ];
            foreach ($getCategoryTreeObjs as $getCategoryTreeObj) {
                $categoryFullPath = $categoryChineseFullPath = "";
                if( $getCategoryTreeObj->cate_first ){
                    $categoryFullPath = $getCategoryTreeObj->cate_first;
                }
                if( $getCategoryTreeObj->cate_second ){
                    $categoryFullPath .= " > " . $getCategoryTreeObj->cate_second;
                }
                if( $getCategoryTreeObj->cate_third ){
                    $categoryFullPath .= " > " . $getCategoryTreeObj->cate_third;
                }
                if( $getCategoryTreeObj->cate_chinese_first ){
                    $categoryChineseFullPath = $getCategoryTreeObj->cate_chinese_first;
                }
                if( $getCategoryTreeObj->cate_chinese_second ){
                    $categoryChineseFullPath .= " > " . $getCategoryTreeObj->cate_chinese_second;
                }
                if( $getCategoryTreeObj->cate_chinese_third ){
                    $categoryChineseFullPath .= " > " . $getCategoryTreeObj->cate_chinese_third;
                }

                $result["categories"][] = [
                    "categoryId"              => $getCategoryTreeObj->category_id,
                    "categoryFullPath"        => $categoryFullPath,
                    "categoryChineseFullPath" => $categoryChineseFullPath,
                ];
            }

            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func getTreeCategory
     * @description '1688에서 수집 한 최상위 카테고리 단위를 계층별 목록으로 반환'
     * @param int $categoryId '카테고리 ID'
     */
    public function getTreeCategory(int $categoryId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $getCategoryObjs = Category::where("parent_cate_id", 0)->where("category_id", $categoryId)->get();
            if( count($getCategoryObjs) == 0 ){
                throw new Exception(CategoryErrorMessageConstant::getNotHaveErrorMessage("PARENT_CATEGORY"));
            }
            $result    = $this->getBuildTree($getCategoryObjs, 0);
            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    public function getBuildTree(Collection $elements, int $parentId = 0): array
    {
        $result = [];
        foreach ($elements as $element) {
            if( $parentId == $element->parent_cate_id ){
                $childObjs = Category::where("parent_cate_id", $element->category_id)->get();
                if( count($childObjs) > 0 ){
                    $element["childs"] = $this->getBuildTree($childObjs, $element->category_id);
                }
                $paramArray = [
                    "id"                    => $element->id,
                    "category_id"           => $element->category_id,
                    "category_name"         => $element->category_name,
                    "category_chinese_name" => $element->category_chinese_name,
                    "leaf"                  => $element->leaf,
                    "level"                 => $element->level,
                    "parent_cate_id"        => $element->parent_cate_id,
                ];
                if( isset($element["childs"]) && !empty($element["childs"])){
                    $paramArray["childs"] = $element["childs"];
                }
                $result[] = $paramArray;
            }
        }
    
        return $result;
    }

    /**
     * @func getMappingCategory
     * @description '1688<->채널 카테고리 맵핑 조회'
     * @param string $channel
     */
    public function getMappingCategory(string $channel): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $getCategoryMappingObjs = CategoryMapping::where("mapping_channel", $channel)
            ->where("mapping_code", "!=", 0)
            ->orderBy("category_id", "asc")
            ->get();

            $result = [
                "mapping_channel" => $channel,
                "result"          => []
            ];
            foreach ($getCategoryMappingObjs as $getCategoryMappingObj) {
                $result["result"][] = [
                    "category_id"     => $getCategoryMappingObj->category_id,
                    "mapping_code"    => $getCategoryMappingObj->mapping_code,
                ];
            }
            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func saveCategory
     * @description '1688 카테고리 endPoint 조회 후 저장'
     */
    public function saveCategory(): void
    {
        $msg = "======================== 실행 시작 ========================";
        debug_log($msg, "saveCategoryLog", "saveCategoryLog");

        $categoryIds = [1038378, 10165, 10166, 127380009, 18, 312, 54];
        // DB::beginTransaction();
        try {
            foreach ($categoryIds as $categoryId) {
                $endPoint = $this->apiDomain . "param2/1/com.alibaba.fenxiao.crossborder/category.translation.getById/" . $this->appKey;
                $payload = [
                    'language'     => 'ko',
                    'categoryId'   => $categoryId,
                    'access_token' => $this->accessToken,
                ];
                $apiResult = $this->apiCurl("post", $endPoint, $payload);
                if( $apiResult["isSuccess"] == true && $apiResult["data"]["result"]["success"] == true ){
                    $categoryData = $apiResult["data"]["result"]["result"];

                    $leaf = $hasChildren = false;
                    if( $categoryData["leaf"] !== "false" && $categoryData["leaf"] !== false ){
                        $leaf = true;
                    }
                    if( isset($categoryData["children"]) && !empty($categoryData["children"])){
                        $hasChildren = true;
                    }

                    $upsertWhere = [
                        "category_name"         => $categoryData["translatedName"],
                        "category_chinese_name" => $categoryData["chineseName"],
                        "leaf"                  => $leaf === false ? "N" : "Y",
                        "level"                 => $categoryData["level"],
                        "parent_cate_id"        => $categoryData["parentCateId"],
                    ];
                    Category::updateOrCreate(
                        ["category_id" => $categoryData["categoryId"]],
                        $upsertWhere
                    );
                    if ($hasChildren === true){
                        $this->saveCategoryRecursively($categoryData);
                    }
                } else {
                    $msg = "카테고리 에러 categoryId: {$categoryId}";
                    debug_log($msg, "saveCategoryLog", "saveCategoryLog", LogLevel::DEBUG);
                }    
            }

            // DB::commit();
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "saveCategoryLog", "saveCategoryLog", LogLevel::ERROR);

            // DB::rollBack();
        }

        $msg = "======================== 실행 종료 ========================";
        debug_log($msg, "saveCategoryLog", "saveCategoryLog");
    }

    public function saveCategoryRecursively(array $categoryData): void
    {
        foreach ($categoryData["children"] as $childCategory) {
            $leaf = false;
            if( $childCategory["leaf"] !== "false" && $childCategory["leaf"] !== false ){
                $leaf = true;
            }
            $upsertWhere = [
                "category_name"         => $childCategory["translatedName"],
                "category_chinese_name" => $childCategory["chineseName"],
                "leaf"                  => $leaf === false ? "N" : "Y",
                "level"                 => $childCategory["level"],
                "parent_cate_id"        => $childCategory["parentCateId"],
            ];
            Category::updateOrCreate(
                ["category_id" => $childCategory["categoryId"]],
                $upsertWhere
            );

            $endPoint = $this->apiDomain . "param2/1/com.alibaba.fenxiao.crossborder/category.translation.getById/" . $this->appKey;
            $payload = [
                'language'     => 'ko',
                'categoryId'   => $childCategory["categoryId"],
                'access_token' => $this->accessToken,
            ];
            $apiResult = $this->apiCurl("post", $endPoint, $payload);
            if ($apiResult["isSuccess"] == true && $apiResult["data"]["result"]["success"] == true) {
                $childCategoryData = $apiResult["data"]["result"]["result"];

                $last_leaf = $last_hasChildren = false;
                if( $childCategoryData["leaf"] !== "false" && $childCategoryData["leaf"] !== false ){
                    $last_leaf = true;
                }
                if( isset($childCategoryData["children"]) && !empty($childCategoryData["children"])){
                    $last_hasChildren = true;
                }

                if ($last_hasChildren === true){
                    $this->saveCategoryRecursively($childCategoryData);
                } else {
                    $upsertWhere = [
                        "category_name"         => $childCategoryData["translatedName"],
                        "category_chinese_name" => $childCategoryData["chineseName"],
                        "leaf"                  => $last_leaf === false ? "N" : "Y",
                        "level"                 => $childCategoryData["level"],
                        "parent_cate_id"        => $childCategoryData["parentCateId"],
                    ];
                    Category::updateOrCreate(
                        ["category_id" => $childCategoryData["categoryId"]],
                        $upsertWhere
                    );
                }
            } else {
                $msg = "카테고리 에러 categoryId: {$childCategory["categoryId"]}";
                debug_log($msg, "saveCategoryLog", "saveCategoryLog", LogLevel::DEBUG);
            }
        }
    }

    /**
     * @func getMallCategory
     * @description '1688 카테고리 endPoint 조회'
     * @param int $categoryId '카테고리 ID'
     */
    public function getMallCategory(int $categoryId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $endPoint = $this->apiDomain . "param2/1/com.alibaba.fenxiao.crossborder/category.translation.getById/" . $this->appKey;
            $payload = [
                'language'     => 'ko',
                'categoryId'   => $categoryId,
                'access_token' => $this->accessToken,
            ];
            $returnMsg = $this->apiCurl("post", $endPoint, $payload);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
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
            $endPoint = $this->apiDomain . "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/" . $this->appKey;
            $payload = [
                'access_token'     => $this->accessToken,
                'offerDetailParam' => [
                    'country' => 'en',
                    'offerId' => $offerId,
                ]
            ];
            $returnMsg = $this->apiCurl("post", $endPoint, $payload);
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
            $endPoint = $this->apiDomain . "param2/1/com.alibaba.fenxiao.crossborder/product.search.keywordQuery/" . $this->appKey;
            $payload = [
                'access_token'    => $this->accessToken,
                'offerQueryParam' => [
                    'keyword'    => '',
                    'beginPage'  => $page,
                    'pageSize'   => $pageSize,
                    'country'    => 'en',
                    'categoryId' => $categoryId,
                ]
            ];
            $apiDatas = $this->apiCurl("POST", $endPoint, $payload);
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
                        $endPoint       = $this->apiDomain . "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/" . $this->appKey;
                        $payload        = [
                            'access_token'     => $this->accessToken,
                            'offerDetailParam' => [
                                'offerId' => $offerId,
                                'country' => 'en',
                            ]
                        ];
                        $detailResult = $this->apiCurl("POST", $endPoint, $payload);
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

                        // if( $isChangeImg == true ){
                        //     $transMainImgResult = $this->openApiAbstract->translateImage($main_img_origin);
                        //     if( $transMainImgResult["isSuccess"] == false || 
                        //         ( isset($transMainImgResult["data"]) && $transMainImgResult["data"]["status"] != "success" )
                        //     ){
                        //         throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_TRANS_IMG"));
                        //     } else {
                        //         $mime           = pathinfo($main_img_origin, PATHINFO_EXTENSION);
                        //         $mainImgName    = "/" . $this->appEnv . date('Y/m/d/') . $offerId . "_main." . $mime;
                        //         $uploadResult   = $this->uploadAbstract->uploadFile($mainImgName, base64_decode($transMainImgResult["data"]["translated_image"]));

                        //         if( $uploadResult == false ){
                        //             throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_S3MG_UPLOAD"));
                        //         } else {
                        //             $main_img_trans = env("AWS_URL") . $mainImgName;
                        //         }
                        //     }
                        // }

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

        $transImgList = [];
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
                $upsertWhere       = [];
                $upsertDetailWhere = [];
                if( $product1688ImageDto->is_change_img == true ){
                    $upsertWhere = [
                        "img_url_trans" => ""
                    ];
                    $upsertDetailWhere = [
                        "width"  => $product1688ImageDto->width,
                        "height" => $product1688ImageDto->height,
                        "byte"   => $product1688ImageDto->byte,
                        "mime"   => $product1688ImageDto->mime,
                    ];

                    $transImgList[] = [
                        "offer_id"       => $product1688ImageDto->offer_id,
                        "img_type"       => $product1688ImageDto->img_type,
                        "img_url_origin" => $product1688ImageDto->img_url_origin,
                    ];
                }
                ProductImageData::updateOrCreate(
                    [
                        "offer_id"       => $product1688ImageDto->offer_id,
                        "img_type"       => $product1688ImageDto->img_type,
                        "img_url_origin" => $product1688ImageDto->img_url_origin,
                    ],
                    $upsertWhere
                );
                ProductImageDetailData::updateOrCreate(
                    [
                        "offer_id"       => $product1688ImageDto->offer_id,
                        "img_type"       => $product1688ImageDto->img_type,
                        "img_url_origin" => $product1688ImageDto->img_url_origin,
                    ],
                    $upsertDetailWhere
                );
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
            // 4. product_option_datas upsert
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
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }
        return $returnMsg;
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
            $errorMsg = ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_CHECK_IMG_SIZE") . " {$imagePath}";
            throw new UnexpectedValueException($errorMsg);
        }

        return [
            "width"  => $imageInfo[0],
            "height" => $imageInfo[1],
            "byte"   => $imageInfo["bits"],
            "mime"   => $imageInfo["mime"],
        ];
    }

    /**
     * @func saveCategoryMapping
     * @description 'categories 테이블의 데이터들을 category_mappings 테이블로 정리'
     */
    public function saveCategoryMapping(): void
    {
        $msg = "======================== 실행 시작 ========================";
        debug_log($msg, "saveCategoryMappingLog", "saveCategoryMappingLog");

        try {
            $filePath          = public_path('app/oc_categories.txt');
            $mappingCategories = [];
            if (File::exists($filePath)) {
                $lines = File::lines($filePath);
                foreach ($lines as $line) {
                    list($ali_code, $oc_code) = explode(' : ', $line);

                    $ali_code = trim($ali_code);
                    $oc_code  = trim($oc_code);
                    if( $ali_code != "미 매핑" || $oc_code != "") {
                        $mappingCategories[$ali_code] = (int)$oc_code;
                    }
                }
            } else {
                throw new Exception("파일이 존재하지 않습니다.");
            }

            $parentCategoryObjs = Category::where("parent_cate_id", 0)->get();
            foreach ($parentCategoryObjs as $parentCategoryObj) {
                $getTreeCategory = $this->getTreeCategory($parentCategoryObj->category_id);
                foreach ($getTreeCategory["data"] as $firstCategory) {
                    $first_cate_first          = $firstCategory["category_name"];
                    $first_chinese_cate_first  = $firstCategory["category_chinese_name"];
                    $first_categoryId          = $firstCategory["category_id"];
                    $first_oc_category_code    = 0;
                    if( isset($mappingCategories[$first_categoryId]) ){
                        $first_oc_category_code = $mappingCategories[$first_categoryId];
                    }
                    if( isset($firstCategory["childs"]) && count($firstCategory["childs"]) > 0 ){
                        foreach ($firstCategory["childs"] as $secondCatogories) {
                            $second_cate_first          = $firstCategory["category_name"];
                            $second_cate_second         = $secondCatogories["category_name"];
                            $second_chinese_cate_first  = $firstCategory["category_chinese_name"];
                            $second_chinese_cate_second = $secondCatogories["category_chinese_name"];
                            $second_categoryId          = $secondCatogories["category_id"];
                            $second_oc_category_code    = 0;
                            if( isset($mappingCategories[$second_categoryId]) ){
                                $second_oc_category_code = $mappingCategories[$second_categoryId];
                            }
                            if( isset($secondCatogories["childs"]) && count($secondCatogories["childs"]) > 0 ){
                                foreach ($secondCatogories["childs"] as $thirdCategory) {
                                    $third_cate_first          = $firstCategory["category_name"];
                                    $third_cate_second         = $secondCatogories["category_name"];
                                    $third_cate_third          = $thirdCategory["category_name"];
                                    $third_chinese_cate_first  = $firstCategory["category_chinese_name"];
                                    $third_chinese_cate_second = $secondCatogories["category_chinese_name"];
                                    $third_chinese_cate_third  = $thirdCategory["category_chinese_name"];
                                    $third_categoryId          = $thirdCategory["category_id"];
                                    $third_oc_category_code    = 0;
                                    if( isset($mappingCategories[$third_categoryId]) ){
                                        $third_oc_category_code = $mappingCategories[$third_categoryId];
                                    }
                                    $third_upsertWhere = [
                                        "cate_first"          => $third_cate_first,
                                        "cate_second"         => $third_cate_second,
                                        "cate_third"          => $third_cate_third,
                                        "cate_chinese_first"  => $third_chinese_cate_first,
                                        "cate_chinese_second" => $third_chinese_cate_second,
                                        "cate_chinese_third"  => $third_chinese_cate_third,
                                    ];
                                    CategoryTree::updateOrCreate(
                                        ["category_id" => $third_categoryId],
                                        $third_upsertWhere
                                    );
                                    CategoryMapping::updateOrCreate(
                                        ["category_id" => $third_categoryId],
                                        [
                                            "mapping_channel" => ProductConstant::MAPPING_OC_CHANNEL,
                                            "mapping_code"    => $third_oc_category_code
                                        ]
                                    );
                                }
                            }
                            $second_upsertWhere = [
                                "cate_first"          => $second_cate_first,
                                "cate_second"         => $second_cate_second,
                                "cate_third"          => null,
                                "cate_chinese_first"  => $second_chinese_cate_first,
                                "cate_chinese_second" => $second_chinese_cate_second,
                                "cate_chinese_third"  => null,
                            ];
                            CategoryTree::updateOrCreate(
                                ["category_id" => $second_categoryId],
                                $second_upsertWhere
                            );
                            CategoryMapping::updateOrCreate(
                                ["category_id" => $second_categoryId],
                                [
                                    "mapping_channel" => ProductConstant::MAPPING_OC_CHANNEL,
                                    "mapping_code"    => $second_oc_category_code
                                ]
                            );
                        }
                    }
                    $first_upsertWhere = [
                        "cate_first"          => $first_cate_first,
                        "cate_second"         => null,
                        "cate_third"          => null,
                        "cate_chinese_first"  => $first_chinese_cate_first,
                        "cate_chinese_second" => null,
                        "cate_chinese_third"  => null,
                    ];
                    CategoryTree::updateOrCreate(
                        ["category_id" => $first_categoryId],
                        $first_upsertWhere
                    );
                    CategoryMapping::updateOrCreate(
                        ["category_id" => $first_categoryId],
                        [
                            "mapping_channel" => ProductConstant::MAPPING_OC_CHANNEL,
                            "mapping_code"    => $first_oc_category_code
                        ]
                    );
                }
            }
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "saveCategoryMappingLog", "saveCategoryMappingLog", LogLevel::ERROR);
        }

        $msg = "======================== 실행 종료 ========================";
        debug_log($msg, "saveCategoryMappingLog", "saveCategoryMappingLog");
    }

    function apiCurl(string $method, string $endPoint, array $payload, array $header = ["Content-Type: application/x-www-form-urlencoded"]): array
    {
        $returnMsg = $this->returnMsg;

        $apiInfo = str_replace($this->apiDomain, "", $endPoint);
        $aliParams = [];
        foreach ($payload as $key => $val) {
            if( is_array($val) ){
                $aliParams[] = $key . json_encode($val);
            }else{
                $aliParams[] = $key . $val;
            }
        }
        sort($aliParams);
        $sign_str  = join('', $aliParams);
        $sign_str  = $apiInfo . $sign_str;
        $code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $this->appSecret, true)));
        $payload["_aop_signature"] = $code_sign;

        $finalPayload = "";
        $index = 0;
        foreach ($payload as $key => $val) {
            if( $index == 0 ){
                if( is_array($val) ){
                    $finalPayload .= $key . "=" . json_encode($val);
                }else{
                    $finalPayload .= $key . "=" . $val;
                }
            }else{
                if( is_array($val) ){
                    $finalPayload .= "&" . $key . "=" . json_encode($val);
                }else{
                    $finalPayload .= "&" . $key . "=" . $val;
                }
            }
            $index++;
        }
        
		$curl   = curl_init();
		$method = strtoupper($method);
		if($method == 'GET') {
			$queryString = (($payload)? http_build_query( $payload ) : '');
			curl_setopt_array($curl, array(
				CURLOPT_URL            => $endPoint.(($queryString)? '?'.$queryString : ''),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_MAXREDIRS      => 10,
				CURLOPT_TIMEOUT        => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST  => $method,
				CURLOPT_HTTPHEADER     => $header
			));
		} else if($method == 'POST'){
			curl_setopt_array($curl, array(
				CURLOPT_URL            => $endPoint,
				CURLOPT_POST           => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_CUSTOMREQUEST  => $method,
				CURLOPT_POSTFIELDS     => $finalPayload,
				CURLOPT_HTTPHEADER     => $header
			));
		}
		$result = curl_exec($curl);
		curl_close($curl);

        try {
            $apiResult = json_decode($result, JSON_UNESCAPED_UNICODE);
            if(!is_array($apiResult)) throw new InvalidArgumentException("결과가 배열이 아닙니다.");

            $returnMsg = helpers_success_message($apiResult);
        } catch (JsonException $e) {
            $returnMsg = helpers_fail_message(false, "결과가 Json이 아닙니다.");
        } catch (InvalidArgumentException $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }
    
}