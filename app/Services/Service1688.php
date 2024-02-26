<?php

namespace App\Services;

use App\Abstracts\ApiModuleAbstract;
use App\Models\Category;
use App\Models\CategoryMapping;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use JsonException;

class Service1688 extends ApiModuleAbstract
{
    private string $appKey;
    private string $appSecret;
    private string $accessToken;

    public function __construct()
    {
        parent::__construct(env("1688_API_DOMAIN"));

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
                $categoryFullPath = "";
                if( $getCategoryMappingObj->cate_first ){
                    $categoryFullPath = $getCategoryMappingObj->cate_first;
                }
                if( $getCategoryMappingObj->cate_second ){
                    $categoryFullPath .= " > " . $getCategoryMappingObj->cate_second;
                }
                if( $getCategoryMappingObj->cate_third ){
                    $categoryFullPath .= " > " . $getCategoryMappingObj->cate_third;
                }

                $result["categories"][] = [
                    "categoryId"       => $getCategoryMappingObj->category_id,
                    "categoryFullPath" => $categoryFullPath,
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
                throw new Exception("최상위 카테고리가 아니거나 수집 된 최상위 카테고리가 없습니다.");
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
                    "id"             => $element->id,
                    "category_id"    => $element->category_id,
                    "category_name"  => $element->category_name,
                    "leaf"           => $element->leaf,
                    "level"          => $element->level,
                    "parent_cate_id" => $element->parent_cate_id,
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
                    debug_log($msg, "saveCategoryLog", "saveCategoryLog");
                }    
            }

            // DB::commit();
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 ========================\r\n";
            $msg .= "error: " . $e->getMessage();
            debug_log($msg, "saveCategoryLog", "saveCategoryLog");

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
                    $msg = json_encode($childCategoryData, JSON_UNESCAPED_UNICODE);
                    debug_log($msg, "saveCategoryLog", "saveCategoryLog");
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
                debug_log($msg, "saveCategoryLog", "saveCategoryLog");
            }
        }
    }

    /**
     * @func get1688Category
     * @description '1688 카테고리 endPoint 조회'
     * @param int $categoryId '카테고리 ID'
     */
    public function get1688Category(int $categoryId): array
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
                $getAllCategories = $this->getAllCategory($parentCategoryObj->category_id);
                foreach ($getAllCategories["data"] as $firstCategory) {
                    $cate_first  = $firstCategory["category_name"];
                    $cate_second = null;
                    $cate_third  = null;
                    $categoryId  = $firstCategory["category_id"];
                    if( isset($firstCategory["childs"]) && count($firstCategory["childs"]) > 0 ){
                        foreach ($firstCategory["childs"] as $secondCatogories) {
                            $cate_second = $secondCatogories["category_name"];
                            $categoryId  = $secondCatogories["category_id"];
                            if( isset($secondCatogories["childs"]) && count($secondCatogories["childs"]) > 0 ){
                                foreach ($secondCatogories["childs"] as $thirdCategory) {
                                    $cate_third = $thirdCategory["category_name"];
                                    $categoryId = $thirdCategory["category_id"];
                                    $upsertWhere = [
                                        "cate_first"  => $cate_first,
                                        "cate_second" => $cate_second,
                                        "cate_third"  => $cate_third,
                                    ];
                                    CategoryMapping::updateOrCreate(
                                        ["category_id" => $categoryId],
                                        $upsertWhere
                                    );
                                }
                            } else {
                                $upsertWhere = [
                                    "cate_first"  => $cate_first,
                                    "cate_second" => $cate_second,
                                    "cate_third"  => $cate_third,
                                ];
                                CategoryMapping::updateOrCreate(
                                    ["category_id" => $categoryId],
                                    $upsertWhere
                                );
                            }
                        }
                    } else {
                        $upsertWhere = [
                            "cate_first"  => $cate_first,
                            "cate_second" => $cate_second,
                            "cate_third"  => $cate_third,
                        ];
                        CategoryMapping::updateOrCreate(
                            ["category_id" => $categoryId],
                            $upsertWhere
                        );
                    }
                }
            }
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 ========================\r\n";
            $msg .= "error: " . $e->getMessage();
            debug_log($msg, "saveCategoryMappingLog", "saveCategoryMappingLog");
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
            $aliParams[] = $key . $val;
        }
        sort($aliParams);
        $sign_str  = join('', $aliParams);
        $sign_str  = $apiInfo . $sign_str;
        $code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $this->appSecret, true)));

        $payload["_aop_signature"] = $code_sign;

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
				CURLOPT_POSTFIELDS     => http_build_query($payload),
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