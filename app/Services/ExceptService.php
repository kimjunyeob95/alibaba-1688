<?php

namespace App\Services;

use App\Constants\Constant1688;
use App\Constants\ExceptConstant;
use App\Models\ProductExceptData;
use App\Models\ProductNoticeData;
use App\Models\WNoticeData;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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

        $builder = WNoticeData::select([
            "w_notice_datas.*",
            "b.except_type",
            "b.is_except as b_is_except",
            DB::raw("GROUP_CONCAT(DISTINCT w_notice_datas.attribute_name SEPARATOR ', ') as attribute_names")
        ])
        ->leftJoin("product_except_datas as b", "w_notice_datas.attribute_id", "=", "b.attribute_id")
        ->where("w_notice_datas.lang", Constant1688::LANGUAGE_KR)
        ->orderBy("w_notice_datas.updated_at", "desc")
        ->groupBy("w_notice_datas.attribute_id");

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
            } else if( $except_type == ExceptConstant::IS_EXCEPT_MODI ){
                $builder->where("w_notice_datas.apply_attribute_name", "!=", "");
            }
        }

        if( !empty($keyword) ){
            $builder->where(function($query1) use ($keyword) {
                $query1->where("w_notice_datas.attribute_name", "like", "%" . $keyword . "%")
                ->orWhere("w_notice_datas.attribute_value", "like", "%" . $keyword . "%")
                ->orWhere("w_notice_datas.apply_attribute_name", "like", "%" . $keyword . "%");
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
