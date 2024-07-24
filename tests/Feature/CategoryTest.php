<?php

namespace Tests\Feature;

use App\Constants\MallConstant;
use App\Constants\ProductConstant;
use App\Models\Category;
use App\Models\CategoryMapping;
use App\Models\CategoryTree;
use App\Models\ChannelCategoryRegistData;
use Exception;
use Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\Paginator;

class CategoryTest extends TestCase
{
    # php artisan test --filter testCategoryWandWAppMapping
    public function testCategoryWandWAppMapping()
    {
        $filePath     = public_path('app/w_wapp_categories_mapping.txt');
        if (File::exists($filePath)) {
            $lines = File::lines($filePath);
            foreach ($lines as $line) {
                $lineArr = explode(',', $line);

                $category_id  = $lineArr[0];
                $mapping_code = $lineArr[1];

                CategoryMapping::updateOrCreate(
                    [
                        "mapping_channel" => ProductConstant::MAPPING_WAPP,
                        "category_id"     => $category_id
                    ],
                    [
                        "mapping_code" => $mapping_code
                    ]
                );
            }
        } else {
            throw new Exception("파일이 존재하지 않습니다.");
        }

        dd("끝");
    }

    # php artisan test --filter testCategoryWAppandOcMapping
    public function testCategoryWAppandOcMapping()
    {
        $filePath     = public_path('app/wapp_oc_categories_mapping.txt');
        if (File::exists($filePath)) {
            $lines = File::lines($filePath);
            foreach ($lines as $line) {
                $lineArr = explode(',', $line);

                $category_id  = $lineArr[0];
                $mapping_code = $lineArr[1];

                $wAppMappingObj = CategoryMapping::where([
                    "mapping_channel" => ProductConstant::MAPPING_WAPP,
                    "category_id"     => $category_id,
                ])->first();
    
                //category_id 와 매핑된 WAPP 카테고리
                $mappingCode = $wAppMappingObj->mapping_code;
    
                //WAPP 카테고리와 매핑된 category_id 전체 조회
                $selCategoryObj = CategoryMapping::select("category_id")
                ->where("mapping_channel", ProductConstant::MAPPING_WAPP)
                ->where("mapping_code", $mappingCode)
                ->get();

                foreach($selCategoryObj as $category){
                    CategoryMapping::updateOrCreate(
                        [
                            "mapping_channel" => MallConstant::MALL_ONCHANNEL,
                            "category_id"     => $category->category_id,
                        ],
                        [
                            "mapping_code" => $mapping_code,
                        ]
                    );
                }
            }
        } else {
            throw new Exception("파일이 존재하지 않습니다.");
        }

        dd("끝");
    }

    # php artisan test --filter testCategoryWAppandOcAllMapping
    public function testCategoryWAppandOcAllMapping()
    {
        $msg = "WApp<->OC 모든 카테고리 맵핑 시작";
        debug_log($msg, "category/testCategoryWAppandOcAllMapping", "testCategoryWAppandOcAllMapping");

        $builder = CategoryMapping::where([
            "mapping_channel" => ProductConstant::MAPPING_WAPP,
        ]);

        $perPage    = 900;
        $totalCount = $builder->count();
        $totalPages = ceil($totalCount / $perPage);

        for ($page = 1; $page <= $totalPages; $page++) {
            
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
            
            // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
            $pagedData = $builder->paginate($perPage);
            $results   = $pagedData->items();

            foreach ($results as $obj) {
                $ocObj = CategoryMapping::where("mapping_channel", MallConstant::MALL_ONCHANNEL)
                ->where("category_id", $obj->category_id)
                ->where("mapping_code", "!=", "")
                ->first();

                if( $ocObj != null ){
                    //WAPP 카테고리와 매핑된 category_id 전체 조회
                    $selCategoryObj = CategoryMapping::select("category_id")
                    ->where("mapping_channel", ProductConstant::MAPPING_WAPP)
                    ->where("mapping_code", $obj->mapping_code)
                    ->get();

                    foreach($selCategoryObj as $category){
                        CategoryMapping::updateOrCreate(
                            [
                                "mapping_channel" => MallConstant::MALL_ONCHANNEL,
                                "category_id"     => $category->category_id,
                            ],
                            [
                                "mapping_code" => $ocObj->mapping_code,
                            ]
                        );
                    }
                }

                // 퍼센트 계산
                $percent = round(($page / $totalPages) * 100);

                $msg = "WApp<->OC 모든 카테고리 맵핑 ({$page}/{$totalPages}) | {$percent}% 완료";
                debug_log($msg, "category/testCategoryWAppandOcAllMapping", "testCategoryWAppandOcAllMapping");
            }
        }

        $msg = "WApp<->OC 모든 카테고리 맵핑 종료";
        debug_log($msg, "category/testCategoryWAppandOcAllMapping", "testCategoryWAppandOcAllMapping");
    }

    # php artisan test --filter testCreateChannelCategory
    public function testCreateChannelCategory()
    {
        $objs = CategoryTree::whereIn("category_id", [10165, 7, 1042954, 1038378, 10166])->get();

        foreach ($objs as $obj) {
            $childrens_1 = Category::where("parent_cate_id", $obj->category_id)->get();

            foreach ($childrens_1 as $childrens_1_obj) {
                ChannelCategoryRegistData::updateOrCreate(
                    [
                        "category_id" => $childrens_1_obj->category_id,
                        "channel"     => MallConstant::MALL_EASYSELL,
                        "send_type"   => MallConstant::EASYSELL_W,
                    ],
                    [
                        "is_regist" => MallConstant::REGIST_Y,
                    ]
                );

                $childrens_2 = Category::where("parent_cate_id", $childrens_1_obj->category_id)->get();
                foreach ($childrens_2 as $childrens_2_obj) {
                    ChannelCategoryRegistData::updateOrCreate(
                        [
                            "category_id" => $childrens_2_obj->category_id,
                            "channel"     => MallConstant::MALL_EASYSELL,
                            "send_type"   => MallConstant::EASYSELL_W,
                        ],
                        [
                            "is_regist" => MallConstant::REGIST_Y,
                        ]
                    );
                }
            }
        }

        dd("끝");
    }

}
