<?php

namespace Tests\Feature;

use App\Constants\MallConstant;
use App\Models\Category;
use App\Models\CategoryTree;
use App\Models\ChannelCategoryRegistData;
use Tests\TestCase;

class CategoryTest extends TestCase
{

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
