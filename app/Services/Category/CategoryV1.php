<?php

namespace App\Services\Category;

use App\Abstracts\CategoryAbstract;
use App\Constants\CategoryErrorMessageConstant;
use App\Constants\Constant1688;
use App\Constants\ProductConstant;
use App\Models\Category;
use App\Models\CategoryMapping;
use App\Models\CategoryTree;
use App\Models\ProductData;
use App\Models\WCategory;
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
     * @param mixed $parent_cate_id
     * @return array
     */
    public function getAllCategory(mixed $parent_cate_id): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $qryBuilder = CategoryTree::with(["category"])->orderBy("cate_first", "asc")->orderBy("cate_second", "asc")->orderBy("cate_third", "asc");
            if( $parent_cate_id != null ){
                $qryBuilder->whereHas('category', function ($query) use ($parent_cate_id) {
                    $query->where("parent_cate_id", $parent_cate_id);
                });
            }
            $getCategoryTreeObjs = $qryBuilder->get();
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
                    "parent_cate_id"          => $getCategoryTreeObj->category->parent_cate_id,
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
                    "category_id"           => $element->category_id,
                    "category_name"         => $element->category_name,
                    "category_chinese_name" => $element->category_chinese_name,
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
            $getCategoryMappingObjs = CategoryMapping::with(["categoryTree"])->where("mapping_channel", $channel)
            ->where("mapping_code", "!=", 0)
            ->orderBy("category_id", "asc")
            ->get();

            $result = [
                "mapping_channel" => $channel,
                "result"          => []
            ];
            foreach ($getCategoryMappingObjs as $getCategoryMappingObj) {
                $categoryFullPath   = $categoryChineseFullPath = "";
                $getCategoryTreeObj = $getCategoryMappingObj->categoryTree;
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

                $result["result"][] = [
                    "category_id"             => $getCategoryMappingObj->category_id,
                    "categoryFullPath"        => $categoryFullPath,
                    "categoryChineseFullPath" => $categoryChineseFullPath,
                    "mapping_code"            => $getCategoryMappingObj->mapping_code,
                ];
            }
            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    public function save1688AllCategory(): void
    {
        $msg = "======================== 실행 시작 ========================";
        debug_log($msg, "save1688AllCategoryLog", "save1688AllCategoryLog");

        $categoryIds = [1038378, 10165, 10166, 127380009, 18, 312, 54];
        $filePath    = public_path('app/1688_categories.json');
        try {
            if (File::exists($filePath)) {
                $jsonContent = File::get($filePath);
                $data        = json_decode($jsonContent, true);
                $result      = $data["result"]["result"]["children"];
            } else {
                throw new Exception("파일이 존재하지 않습니다.");
            }

            foreach ($result as $cate) {
                $categoryId = $cate["categoryId"];
                if( in_array($categoryId, $categoryIds) ) continue;

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
                        $this->saveCategoryRecursively($categoryData, "save1688AllCategoryLog");
                    }
                } else {
                    $msg = "카테고리 에러 categoryId: {$categoryId}";
                    debug_log($msg, "save1688AllCategoryLog", "save1688AllCategoryLog", LogLevel::DEBUG);
                }    
            }
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "save1688AllCategoryLog", "save1688AllCategoryLog", LogLevel::ERROR);
        }

        $msg = "======================== 실행 종료 ========================";
        debug_log($msg, "save1688AllCategoryLog", "save1688AllCategoryLog");
    }

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

    public function saveCategoryRecursively(array $categoryData, string $logName = "saveCategoryLog"): void
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
                debug_log($msg, $logName, $logName, LogLevel::DEBUG);
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

    public function saveWCategory(): void
    {
        try {
            $filePath          = public_path('app/w_categories.txt');
            if (File::exists($filePath)) {
                $lines = File::lines($filePath);
                foreach ($lines as $line) {
                    $data         = explode(',', $line);
                    $mapping_code = trim($data[0]);
                    $cate_first   = "";
                    $cate_second  = "";
                    $cate_third   = "";
                    $cate_fourth  = "";

                    if( isset($data[1]) ){
                        $cate_first = trim($data[1]);
                    }
                    if( isset($data[2]) ){
                        $cate_second = trim($data[2]);
                    }
                    if( isset($data[3]) ){
                        $cate_third = trim($data[3]);
                    }
                    if( isset($data[4]) ){
                        $cate_fourth = trim($data[4]);
                    }
                    $upsertWhere = [
                        "mapping_code" => $mapping_code
                    ];
                    WCategory::updateOrCreate(
                        [
                            "cate_first"  => $cate_first,
                            "cate_second" => $cate_second,
                            "cate_third"  => $cate_third,
                            "cate_fourth" => $cate_fourth,
                        ],
                        $upsertWhere
                    );
                }
            } else {
                throw new Exception("파일이 존재하지 않습니다.");
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
        dd("끝");
    }

    public function saveWCategoryMapping(): void
    {
        try {
            $filePath          = public_path('app/w_categories_mapping.txt');
            $mappingCategories = [];
            if (File::exists($filePath)) {
                $lines = File::lines($filePath);
                foreach ($lines as $line) {
                    $data         = explode(',', $line);
                    $category_id  = trim($data[0]);
                    $mapping_code = trim($data[1]);
                    $cate_first   = "";
                    $cate_second  = "";
                    $cate_third   = "";
                    $cate_fourth  = "";
                    if( $category_id == "미 매핑" ) continue;

                    if( isset($data[2]) ){
                        $cate_first  = trim($data[2]);
                    }
                    if( isset($data[3]) ){
                        $cate_second  = trim($data[3]);
                    }
                    if( isset($data[4]) ){
                        $cate_third  = trim($data[4]);
                    }
                    if( isset($data[5]) ){
                        $cate_fourth  = trim($data[5]);
                    }
                    $mappingCategories[$category_id] = [
                        "mapping_code" => $mapping_code,
                        "cate_first"   => $cate_first,
                        "cate_second"  => $cate_second,
                        "cate_third"   => $cate_third,
                        "cate_fourth"  => $cate_fourth
                    ];
                }
            } else {
                throw new Exception("파일이 존재하지 않습니다.");
            }

            foreach ($mappingCategories as $category_id => $cate) {
                $upsertWhere = [
                    "mapping_code" => $cate["mapping_code"],
                ];
                CategoryMapping::updateOrCreate(
                    [
                        "category_id"     => $category_id,
                        "mapping_channel" => ProductConstant::MAPPING_WAPP
                    ],
                    $upsertWhere
                );
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
        dd("끝");
    }

    public function cateList(array $params): array
    {
        $returnMsg = $this->returnMsg;
        
        try {
            $page           = $params["page"];
            $pageSize       = $params["pageSize"];
            $keyword        = $params["keyword"];
            $mapping_status = $params["mapping_status"];
            $cate_first     = $params["cate_first"];
            $cate_second    = $params["cate_second"];
            $cate_third     = $params["cate_third"];

            $firstCateObjs  = [];
            $secondCateObjs = [];
            $thirdCateObjs  = [];
            if( $cate_first == "" ){
                $firstCateObjs = Category::where("parent_cate_id", 0)->orderBy("category_name", "asc")->get();
            }else {
                if( $cate_first && $cate_second ){
                    $firstCateObjs  = Category::where("parent_cate_id", 0)->orderBy("category_name", "asc")->get();
                    $secondCateObjs = Category::where("parent_cate_id", $cate_first)->orderBy("category_name", "asc")->get();
                    $thirdCateObjs  = Category::where("parent_cate_id", $cate_second)->orderBy("category_name", "asc")->get();
                } else if( $cate_first ){
                    $firstCateObjs  = Category::where("parent_cate_id", 0)->orderBy("category_name", "asc")->get();
                    $secondCateObjs = Category::where("parent_cate_id", $cate_first)->orderBy("category_name", "asc")->get();
                }
            }

            $qryBuilder = CategoryTree::from("category_trees as a")
            ->select([
                "a.*",
                "c.mapping_code",
                "c.cate_first as w_first",
                "c.cate_second as w_second",
                "c.cate_third as w_third",
                "c.cate_fourth as w_fourth"
            ])
            ->leftJoin("category_mappings as b", function($join) {
                $join->on("a.category_id", "=", "b.category_id")
                     ->where("b.mapping_channel", "=", ProductConstant::MAPPING_WAPP);
            })
            ->leftJoin("w_categories as c", "b.mapping_code", "=", "c.mapping_code")
            ->orderBy("a.cate_first", "asc")
            ->orderBy("a.cate_second", "asc")
            ->orderBy("a.cate_third", "asc");
            
            if( $mapping_status == ProductConstant::MAPPING_STATUS_Y ){
                $qryBuilder->where("b.category_id", "!=", null);
            } else if( $mapping_status == ProductConstant::MAPPING_STATUS_N ){
                $qryBuilder->where("b.category_id", null);
            }

            if( $cate_third ){
                $qryBuilder->where("a.category_id", $cate_third);
            } else if( $cate_second ){
                $searchArr = [$cate_second];
                foreach ($thirdCateObjs as $thirdCateObj) {
                    $searchArr[] = $thirdCateObj->category_id;
                }
                $qryBuilder->whereIn("a.category_id", $searchArr);
            } else if( $cate_first ){
                $searchArr = [$cate_first];
                foreach ($secondCateObjs as $secondCateObj) {
                    $searchArr[] = $secondCateObj->category_id;
                    $thirdObjs   = Category::select("category_id")->where("parent_cate_id", $secondCateObj->category_id)->get();
                    foreach ($thirdObjs as $thirdObj) {
                        $searchArr[] = $thirdObj->category_id;
                    }
                }
                $qryBuilder->whereIn("a.category_id", $searchArr);
            }

            if( !empty($keyword) ){
                $qryBuilder->where(function($query1) use ($keyword) {
                    $query1->where("a.cate_first", "like", "%" . $keyword . "%")
                         ->orWhere("a.cate_second", "like", "%" . $keyword . "%")
                         ->orWhere("a.cate_third", "like", "%" . $keyword . "%")
                    ->orWhere(function($query) use ($keyword) {
                        $query->where("c.cate_first", "like", "%" . $keyword . "%")
                            ->orWhere("c.cate_second", "like", "%" . $keyword . "%")
                            ->orWhere("c.cate_third", "like", "%" . $keyword . "%")
                            ->orWhere("c.cate_fourth", "like", "%" . $keyword . "%");
                    })
                    ->orWhere(function($query) use ($keyword) {
                        $query->where("a.category_id", "like", "%" . $keyword . "%")
                            ->orWhere("c.mapping_code", "like", "%" . $keyword . "%");
                    });
                });
            }

            $lists = $qryBuilder->paginate($pageSize)->appends($params);
            $result = [
                "paginator"      => $lists,
                "firstCateObjs"  => $firstCateObjs,
                "secondCateObjs" => $secondCateObjs,
                "thirdCateObjs"  => $thirdCateObjs,
            ];
            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    public function getW(array $params): array
    {
        $returnMsg = $this->returnMsg;
        
        try {
            $cate_first  = $params["cate_first"];
            $cate_second = $params["cate_second"];
            $cate_third  = $params["cate_third"];
            $cate_fourth = $params["cate_fourth"];
            $keyword     = $params["keyword"];

            $builder = WCategory::query();
            
            if( $cate_first != "" ){
                $builder->where("cate_first", $cate_first);
            }
            if( $cate_second != "" ){
                $builder->where("cate_second", $cate_second);
            }
            if( $cate_third != "" ){
                $builder->where("cate_third", $cate_third);
            }
            if( $cate_fourth != "" ){
                $builder->where("cate_fourth", $cate_fourth);
            }

            $builder->orderBy("cate_first", "asc")
            ->orderBy("cate_second", "asc")
            ->orderBy("cate_third", "asc")
            ->orderBy("cate_fourth", "asc");

            if( $keyword != "" ){
                $builder->where(function($query1) use ($keyword) {
                    $query1->where("cate_first", "like", "%" . $keyword . "%")
                    ->orWhere("cate_second", "like", "%" . $keyword . "%")
                    ->orWhere("cate_third", "like", "%" . $keyword . "%")
                    ->orWhere("cate_fourth", "like", "%" . $keyword . "%")
                    ->orWhere("mapping_code", "like", "%" . $keyword . "%");
                });
            }

            $data = $builder->get();
            $returnMsg = helpers_success_message($data);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }
        return $returnMsg;
    }

    public function wMapping(array $params): array
    {
        $returnMsg = $this->returnMsg;
        
        try {
            $category_ids = $params["category_ids"];
            $w_cate_id    = $params["w_cate_id"];
            foreach ($category_ids as $category_id) {
                $cateObj = WCategory::where("id", $w_cate_id)->first();
                if( $cateObj != null ){
                    if( $cateObj->mapping_code ){
                        // 1. 카테고리 맵핑 upsert
                        $upsertWhere = [
                            "mapping_code" => $cateObj->mapping_code
                        ];
                        CategoryMapping::updateOrCreate(
                            [
                                "category_id"     => $category_id,
                                "mapping_channel" => ProductConstant::MAPPING_WAPP
                            ],
                            $upsertWhere
                        );

                        // 2. 상품 mapping_status 업데이트
                        ProductData::where("category_id", $category_id)->update([
                            "mapping_status" => ProductConstant::MAPPING_STATUS_Y
                        ]);
                    }
                }
            }
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }
        return $returnMsg;
    }

    public function getDepth(int $categoryId): array
    {
        $returnMsg = $this->returnMsg;
        
        try {
            $cateObj = Category::where("category_id", $categoryId)->first();
            if( $cateObj == null ){
                throw new Exception(CategoryErrorMessageConstant::getNotHaveErrorMessage("CATEGORY"));
            }

            $nextCateObjs = Category::where("parent_cate_id", $categoryId)->orderBy("category_name", "asc")->get();
            $datas = [];
            foreach ($nextCateObjs as $nextCateObj) {
                $datas[] = [
                    "category_id"   => $nextCateObj->category_id,
                    "category_name" => $nextCateObj->category_name,
                ];
            }
            $returnMsg = helpers_success_message($datas);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    public function getWDepth(array $params): array
    {
        $returnMsg = $this->returnMsg;
        
        try {
            $level     = $params["level"];
            $cate_name = $params["cate_name"];

            $cate_first  = "";
            $cate_second = "";
            $cate_third  = "";
            $cate_arr    = explode(",", $cate_name);
            $where = [];
            $group = [];
            if( $level == 1 ){
                $cate_first = $cate_arr[0];
                $where = [
                    "cate_first" => $cate_first
                ];
                $group = ["cate_second"];
            } else if( $level == 2){
                $cate_first  = $cate_arr[0];
                $cate_second = $cate_arr[1];
                $where = [
                    "cate_first"  => $cate_first,
                    "cate_second" => $cate_second,
                ];
                $group = ["cate_third"];
            } else if( $level == 3){
                $cate_first  = $cate_arr[0];
                $cate_second = $cate_arr[1];
                $cate_third  = $cate_arr[2];
                $where = [
                    "cate_first"  => $cate_first,
                    "cate_second" => $cate_second,
                    "cate_third"  => $cate_third,
                ];
                $group = ["cate_fourth"];
            }

            $nextCateObjs = WCategory::where($where)
            ->orderBy("cate_first", "asc")
            ->orderBy("cate_second", "asc")
            ->orderBy("cate_third", "asc")
            ->orderBy("cate_fourth", "asc")
            ->groupBy($group)->get();

            $data = [];
            foreach ($nextCateObjs as $nextCateObj) {
                $data[] = [
                    "cate_first"  => $nextCateObj->cate_first,
                    "cate_second" => $nextCateObj->cate_second,
                    "cate_third"  => $nextCateObj->cate_third,
                    "cate_fourth" => $nextCateObj->cate_fourth,
                ];
            }
            $returnMsg = helpers_success_message($data);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }
        return $returnMsg;
    }

    public function getInfos(array $categoryIds): array
    {
        $returnMsg = $this->returnMsg;
        
        try {
            $cateObjs = CategoryTree::whereIn("category_id", $categoryIds)->get();
            $cateResult = [];
            foreach ($cateObjs as $cateObj) {
                $cate_name = "";
    
                if( $cateObj->cate_first ){
                    $cate_name = $cateObj->cate_first;
                }
                if( $cateObj->cate_second ){
                    $cate_name .= " > " . $cateObj->cate_second;
                }
                if( $cateObj->cate_third ){
                    $cate_name .= " > " . $cateObj->cate_third;
                }

                $cateResult[] = [
                    "category_id" => $cateObj->category_id,
                    "cate_name"   => $cate_name,
                ];
            }

            $wCateDepth1 = WCategory::select(["id", "cate_first"])->orderBy("cate_first")->groupBy("cate_first")->get();

            $result = [
                "cateResult"  => $cateResult,
                "wCateDepth1" => $wCateDepth1,
            ];
            
            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    public function saveCategoryTree(): void
    {
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
                }
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }

        dd("실행 종료");
    }
}