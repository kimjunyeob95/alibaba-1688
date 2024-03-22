<?php

namespace App\Services\Category;

use App\Abstracts\CategoryAbstract;
use App\Constants\CategoryErrorMessageConstant;
use App\Constants\Constant1688;
use App\Constants\ProductConstant;
use App\Models\Category;
use App\Models\CategoryMapping;
use App\Models\CategoryTree;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Psr\Log\LogLevel;

class CategoryV1 extends CategoryAbstract
{
    private array $returnMsg;
    private string $accessToken;

    public function __construct()
    {
        $this->returnMsg   = helpers_fail_message();
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
                $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/category.translation.getById/";
                $payload = [
                    'language'     => Constant1688::LANGUAGE_KO,
                    'categoryId'   => $categoryId,
                    'access_token' => $this->accessToken,
                ];
                $apiResult = curl_1688("post", $endPoint, $payload);
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

            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/category.translation.getById/";
            $payload  = [
                'language'     => Constant1688::LANGUAGE_KO,
                'categoryId'   => $childCategory["categoryId"],
                'access_token' => $this->accessToken,
            ];
            $apiResult = curl_1688("post", $endPoint, $payload);
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
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/category.translation.getById/";
            $payload = [
                'language'     => Constant1688::LANGUAGE_KO,
                'categoryId'   => $categoryId,
                'access_token' => $this->accessToken,
            ];
            $returnMsg = curl_1688("post", $endPoint, $payload);
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

}