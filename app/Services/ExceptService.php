<?php

namespace App\Services;

use App\Constants\ExceptConstant;
use App\Models\ProductExceptData;
use App\Models\ProductNoticeData;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class ExceptService
{
    protected array $returnMsg;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    public function noticeList(array $params): LengthAwarePaginator
    {
        $except_type = $params["except_type"];
        $keyword     = $params["keyword"];
        $pageSize    = $params["pageSize"];

        $builder = ProductNoticeData::select([
            "product_notice_datas.*",
            "b.except_type",
            "b.is_except as b_is_except",
        ])
        ->leftJoin("product_except_datas as b", "product_notice_datas.attribute_id", "=", "b.attribute_id")
        ->groupBy("product_notice_datas.attribute_id");

        if( !empty($except_type) ){
            if( $except_type == ExceptConstant::IS_EXCEPT_Y ){
                $builder->where("b.except_type", ExceptConstant::EXCEPT_NOTICE);
                $builder->where("b.is_except", ExceptConstant::IS_EXCEPT_Y);
            } else if( $except_type == ExceptConstant::IS_EXCEPT_N ){
                $builder->where(function($query1) {
                    $query1->where("b.except_type", ExceptConstant::EXCEPT_NOTICE)
                    ->where("b.is_except", ExceptConstant::IS_EXCEPT_N)
                    ->orWhereNull("b.id");
                });
            }
        }

        if( !empty($keyword) ){
            $builder->where(function($query1) use ($keyword) {
                $query1->where("product_notice_datas.attribute_name", "like", "%" . $keyword . "%")
                ->orWhere("product_notice_datas.attribute_name_kr", "like", "%" . $keyword . "%")
                ->orWhere("product_notice_datas.attribute_name_en", "like", "%" . $keyword . "%")
                ->orWhere("product_notice_datas.attribute_value", "like", "%" . $keyword . "%")
                ->orWhere("product_notice_datas.attribute_value_kr", "like", "%" . $keyword . "%")
                ->orWhere("product_notice_datas.attribute_value_en", "like", "%" . $keyword . "%")
                ->orWhere("product_notice_datas.attribute_id", "like", "%" . $keyword . "%");
            });
        }

        $lists = $builder->paginate($pageSize)->appends($params);

        return $lists;
    }

    public function noticeUpdate(array $attributeIds, string $is_except = ExceptConstant::IS_EXCEPT_N)
    {
        $returnMsg = $this->returnMsg;
        try {
            
            foreach ($attributeIds as $attributeId) {
                ProductExceptData::updateOrCreate([
                    "except_type"  => ExceptConstant::EXCEPT_NOTICE,
                    "attribute_id" => $attributeId
                ],[
                    "is_except" => $is_except
                ]);
            }

            ProductNoticeData::whereIn("attribute_id", $attributeIds)->update([
                "is_except" => $is_except
            ]);

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
