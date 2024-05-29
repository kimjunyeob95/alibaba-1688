<?php

namespace Tests\Feature;

use App\Constants\Constant1688;
use App\Constants\GosiConstants;
use App\Models\ProductNoticeData;
use App\Models\WNoticeData;
use Tests\TestCase;
use Illuminate\Pagination\Paginator;

class NoticeTest extends TestCase
{

    # W 고시 등록
    # php artisan test --filter testNoticeCreateTest
    public function testNoticeCreateTest()
    {
        $builder = ProductNoticeData::query();

        $perPage = 900;

        $totalCount = $builder->count();
        $totalPages = ceil($totalCount / $perPage);

        for ($page = 1; $page <= $totalPages; $page++) {
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        
            // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
            $pagedData = $builder->paginate($perPage);
            $results   = $pagedData->items();

            foreach ($results as $noticeObj) {
                if( $noticeObj->attribute_name || $noticeObj->attribute_value ){
                    $cnNotice = WNoticeData::where([
                        'attribute_id'    => $noticeObj->attribute_id,
                        'lang'            => Constant1688::LANGUAGE_CN,
                        'attribute_name'  => $noticeObj->attribute_name,
                        'attribute_value' => $noticeObj->attribute_value
                    ])->first();
                    if ( $cnNotice == null ) {
                        WNoticeData::create([
                            'attribute_id'    => $noticeObj->attribute_id,
                            'lang'            => Constant1688::LANGUAGE_CN,
                            'attribute_name'  => $noticeObj->attribute_name,
                            'attribute_value' => $noticeObj->attribute_value
                        ]);
                    }
                }

                if( $noticeObj->attribute_name_kr || $noticeObj->attribute_value_kr ){
                    $krNotice = WNoticeData::where([
                        'attribute_id'    => $noticeObj->attribute_id,
                        'lang'            => Constant1688::LANGUAGE_KR,
                        'attribute_name'  => $noticeObj->attribute_name_kr,
                        'attribute_value' => $noticeObj->attribute_value_kr
                    ])->first();
                    if ( $krNotice == null ) {
                        WNoticeData::create([
                            'attribute_id'    => $noticeObj->attribute_id,
                            'lang'            => Constant1688::LANGUAGE_KR,
                            'attribute_name'  => $noticeObj->attribute_name_kr,
                            'attribute_value' => $noticeObj->attribute_value_kr
                        ]);
                    }
                }

                if( $noticeObj->attribute_name_en || $noticeObj->attribute_value_en ){
                    $enNotice = WNoticeData::where([
                        'attribute_id'    => $noticeObj->attribute_id,
                        'lang'            => Constant1688::LANGUAGE_EN,
                        'attribute_name'  => $noticeObj->attribute_name_en,
                        'attribute_value' => $noticeObj->attribute_value_en
                    ])->first();
                    if ( $enNotice == null ) {
                        WNoticeData::create([
                            'attribute_id'    => $noticeObj->attribute_id,
                            'lang'            => Constant1688::LANGUAGE_EN,
                            'attribute_name'  => $noticeObj->attribute_name_en,
                            'attribute_value' => $noticeObj->attribute_value_en
                        ]);
                    }
                }
            }
        }

        dd("끝");
    }

    # W 고시값 빈값 제외처리
    # php artisan test --filter testNoticeValueUpdate
    public function testNoticeValueUpdate()
    {
        $builder = ProductNoticeData::query();

        $perPage = 900;

        $totalCount = $builder->count();
        $totalPages = ceil($totalCount / $perPage);

        for ($page = 1; $page <= $totalPages; $page++) {
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        
            // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
            $pagedData = $builder->paginate($perPage);
            $results   = $pagedData->items();

            foreach ($results as $noticeObj) {
                if( trim($noticeObj->attribute_value_kr) == "" ){
                    ProductNoticeData::where("id", $noticeObj->id)->update([
                        "is_except" => GosiConstants::IS_EXCEPT_Y
                    ]);
                }
            }
        }

        dd("끝");
    }

    # W 고시 삭제
    # php artisan test --filter testNoticeDeleteTest
    public function testNoticeDeleteTest()
    {
        $builder = WNoticeData::select("*")
        ->groupBy("attribute_id")
        ->groupBy("lang")
        ->groupBy("attribute_name");

        $objs = $builder->get();
        foreach ($objs as $obj) {
            WNoticeData::where([
                "attribute_id"   => $obj->attribute_id,
                "lang"           => $obj->lang,
                "attribute_name" => $obj->attribute_name,
            ])
            ->where("id", "!=", $obj->id)
            ->forceDelete();
        }
        dd("끝");
    }
}
