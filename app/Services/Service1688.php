<?php

namespace App\Services;

use App\Abstracts\ApiModuleAbstract;
use App\Constants\CategoryErrorMessageConstant;
use App\Constants\ProductErrorMessageConstant;
use App\Models\Category;
use App\Models\CategoryMapping;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;
use JsonException;
use Psr\Log\LogLevel;

class Service1688 extends ApiModuleAbstract
{
    private string $appKey;
    private string $appSecret;
    private string $accessToken;

    public function __construct()
    {
        parent::__construct(env("1688_API_DOMAIN", "https://gw.open.1688.com/openapi/"));

        $this->appKey      = env("1688_APP_KEY");
        $this->appSecret   = env("1688_APP_SECRET_KEY");
        $this->accessToken = env("1688_ACCESS_TOKEN");
    }
    
    /**
     * @func getAllCategory
     * @description '1688에서 수집 한 카테고리를 단계별로 정리한 데이터 목록'
     */
    public function getAllCategory(): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $getCategoryMappingObjs = CategoryMapping::orderBy("cate_first", "asc")->orderBy("cate_second", "asc")->orderBy("cate_third", "asc")->get();
            $result = [
                "total" => count($getCategoryMappingObjs),
            ];
            foreach ($getCategoryMappingObjs as $getCategoryMappingObj) {
                $categoryFullPath = $categoryChineseFullPath = "";
                if( $getCategoryMappingObj->cate_first ){
                    $categoryFullPath = $getCategoryMappingObj->cate_first;
                }
                if( $getCategoryMappingObj->cate_second ){
                    $categoryFullPath .= " > " . $getCategoryMappingObj->cate_second;
                }
                if( $getCategoryMappingObj->cate_third ){
                    $categoryFullPath .= " > " . $getCategoryMappingObj->cate_third;
                }
                if( $getCategoryMappingObj->cate_chinese_first ){
                    $categoryChineseFullPath = $getCategoryMappingObj->cate_chinese_first;
                }
                if( $getCategoryMappingObj->cate_chinese_second ){
                    $categoryChineseFullPath .= " > " . $getCategoryMappingObj->cate_chinese_second;
                }
                if( $getCategoryMappingObj->cate_chinese_third ){
                    $categoryChineseFullPath .= " > " . $getCategoryMappingObj->cate_chinese_third;
                }

                $result["categories"][] = [
                    "categoryId"              => $getCategoryMappingObj->category_id,
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
     * @func saveMallProduct
     * @description '1688 상품수집'
     */
    public function saveMallProduct(int $categoryId): void
    {
        $msg = "======================== 실행 시작 (categoryId: {$categoryId}) ========================";
        debug_log($msg, "saveMallProduct", "saveMallProduct");

        $page     = 1;
        $pageSize = 50;
        try {
            $this->saveMallProductRecursively($categoryId, $page, $pageSize);
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 (categoryId: {$categoryId}) ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "saveMallProduct", "saveMallProduct", LogLevel::ERROR);
        }

        $msg = "======================== 실행 종료 (categoryId: {$categoryId}) ========================";
        debug_log($msg, "saveMallProduct", "saveMallProduct");
    }

    public function saveMallProductRecursively(int $categoryId, int $page, int $pageSize, int $totalPage = 0): void
    {
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
                foreach ($productDatas as $key => $productData) {
                    try {
                        $offerId        = $productData["offerId"];
                        $errorMsgDetail = ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL") . " | offerId: {$offerId} | categoryId: {$categoryId} | page: {$page}";
                        $endPoint       = $this->apiDomain . "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/" . $this->appKey;
                        $payload        = [
                            'access_token'     => $this->accessToken,
                            'offerDetailParam' => [
                                'offerId' => $offerId,
                                'country' => 'en',
                            ]
                        ];
                        $detailResult = $this->apiCurl("POST", $endPoint, $payload);
                        if( $detailResult["isSuccess"] != true ){
                            throw new Exception($detailResult["msg"] . " | " . $errorMsgDetail);
                        }
                        if( $detailResult["data"]["result"]["success"] != true ){
                            throw new Exception($errorMsgDetail);
                        }
                        $detailProduct = $detailResult["data"]["result"]["result"];
                    } catch (Exception $de) {
                        $msg = $de->getMessage();
                        debug_log($msg, "saveMallProduct", "saveMallProduct", LogLevel::DEBUG);
                    }
                }
            } else {
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT_SEARCH_KEYWORDQUERY") . " | categoryId: {$categoryId} | page: {$page}");
            }

            $totalPage = $apiDatas["data"]["result"]["result"]["totalPage"];
            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveMallProductRecursively($categoryId, $nextPage, $pageSize, $totalPage);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            debug_log($msg, "saveMallProduct", "saveMallProduct", LogLevel::DEBUG);

            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveMallProductRecursively($categoryId, $nextPage, $pageSize, $totalPage);
            }
        }
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
            $parentCategoryObjs = Category::where("parent_cate_id", 0)->get();
            foreach ($parentCategoryObjs as $parentCategoryObj) {
                $getTreeCategory = $this->getTreeCategory($parentCategoryObj->category_id);
                foreach ($getTreeCategory["data"] as $firstCategory) {
                    $first_cate_first          = $firstCategory["category_name"];
                    $first_chinese_cate_first  = $firstCategory["category_chinese_name"];
                    $first_categoryId          = $firstCategory["category_id"];
                    if( isset($firstCategory["childs"]) && count($firstCategory["childs"]) > 0 ){
                        foreach ($firstCategory["childs"] as $secondCatogories) {
                            $second_cate_first          = $firstCategory["category_name"];
                            $second_cate_second         = $secondCatogories["category_name"];
                            $second_chinese_cate_first  = $firstCategory["category_chinese_name"];
                            $second_chinese_cate_second = $secondCatogories["category_chinese_name"];
                            $second_categoryId          = $secondCatogories["category_id"];
                            if( isset($secondCatogories["childs"]) && count($secondCatogories["childs"]) > 0 ){
                                foreach ($secondCatogories["childs"] as $thirdCategory) {
                                    $third_cate_first          = $firstCategory["category_name"];
                                    $third_cate_second         = $secondCatogories["category_name"];
                                    $third_cate_third          = $thirdCategory["category_name"];
                                    $third_chinese_cate_first  = $firstCategory["category_chinese_name"];
                                    $third_chinese_cate_second = $secondCatogories["category_chinese_name"];
                                    $third_chinese_cate_third  = $thirdCategory["category_chinese_name"];
                                    $third_categoryId          = $thirdCategory["category_id"];
                                    $third_upsertWhere         = [
                                        "cate_first"          => $third_cate_first,
                                        "cate_second"         => $third_cate_second,
                                        "cate_third"          => $third_cate_third,
                                        "cate_chinese_first"  => $third_chinese_cate_first,
                                        "cate_chinese_second" => $third_chinese_cate_second,
                                        "cate_chinese_third"  => $third_chinese_cate_third,
                                    ];
                                    CategoryMapping::updateOrCreate(
                                        ["category_id" => $third_categoryId],
                                        $third_upsertWhere
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
                            CategoryMapping::updateOrCreate(
                                ["category_id" => $second_categoryId],
                                $second_upsertWhere
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
                    CategoryMapping::updateOrCreate(
                        ["category_id" => $first_categoryId],
                        $first_upsertWhere
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