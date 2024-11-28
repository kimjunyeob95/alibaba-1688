<?php
namespace App\Console\Commands;

use App\Constants\BonaeraConstant;
use App\Models\BonaeraInBaseData;
use App\Models\BonaeraOutBaseData;
use App\Services\Wms\WmsService;
use App\Services\Wms\WmsW1;
use Illuminate\Console\Command;
use Illuminate\Pagination\Paginator;

class WmsCommand extends Command
{
    protected $signature   = 'wms_command {--func=} {--ids=}';
    protected $description = 'wms command';

    protected WmsService $wmsService;

    public function __construct()
    {
        parent::__construct();
        $this->wmsService = new WmsService(app(WmsW1::class));
    }

    public function handle()
    {
        $func = $this->option('func');

        if( !$func ) return null;

        switch ($func) {
            /**
             * 입고신청
             * php artisan wms_command --func=bonaeraInFailCreate --ids=1
             */
            case 'bonaeraInFailCreate':
                $ids = explode(",", $this->option('ids'));
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $this->wmsService->bonaeraInFailCreate($id);
                    }
                }
                break;
            /**
             * 입고정보 업데이트
             * php artisan wms_command --func=bonaeraInUpdate --ids=1
             */
            case 'bonaeraInUpdate':
                $ids = explode(",", $this->option('ids'));
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $this->wmsService->bonaeraInUpdate($id);
                    }
                }
                break;
            /**
             * 출고신청
             * php artisan wms_command --func=bonaeraOutCreate --ids=4
             */
            case 'bonaeraOutCreate':
                $ids = explode(",", $this->option('ids'));
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $this->wmsService->bonaeraOutCreate($id);
                    }
                }
                break;
            /**
             * 출고정보 업데이트
             * php artisan wms_command --func=bonaeraOutUpdate --ids=1
             */
            case 'bonaeraOutUpdate':
                $ids = explode(",", $this->option('ids'));
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $this->wmsService->bonaeraOutUpdate($id);
                    }
                }
                break;
            /**
             * 출고 배송비 결제
             * php artisan wms_command --func=bonaeraOutPay --ids=1
             */
            case 'bonaeraOutPay':
                $ids = explode(",", $this->option('ids'));
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $this->wmsService->bonaeraOutPay($id);
                    }
                }
                break;
            /**
             * 입고정보 배치 업데이트
             * php artisan wms_command --func=batchInUpdate
             */
            case 'batchInUpdate':
                $builder    = BonaeraInBaseData::select(["id"]);
                $perPage    = 900;
                $totalCount = count($builder->get());
                $totalPages = ceil($totalCount / $perPage);

                for ($page = 1; $page <= $totalPages; $page++) {
                    Paginator::currentPageResolver(function () use ($page) {
                        return $page;
                    });
                    
                    // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
                    $pagedData = $builder->paginate($perPage);
                    $results   = $pagedData->pluck('id')->toArray();
                    foreach ($results as $id) {
                        $this->wmsService->bonaeraInUpdate($id);
                    }
                }
                break;
            /**
             * 출고정보 배치 업데이트
             * php artisan wms_command --func=batchOutUpdate
             */
            case 'batchOutUpdate':
                $builder = BonaeraOutBaseData::select(["bonaera_out_base_datas.id"])
                ->join("bonaera_out_delivery_datas as b", "bonaera_out_base_datas.group_no", "=", "b.group_no")
                ->whereIn("b.state", BonaeraConstant::BATCH_GROUP_STATUS)
                ->groupBy("bonaera_out_base_datas.id");

                $perPage    = 900;
                $totalCount = count($builder->get());
                $totalPages = ceil($totalCount / $perPage);

                for ($page = 1; $page <= $totalPages; $page++) {
                    Paginator::currentPageResolver(function () use ($page) {
                        return $page;
                    });
                    
                    // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
                    $pagedData = $builder->paginate($perPage);
                    $results   = $pagedData->pluck('id')->toArray();
                    foreach ($results as $id) {
                        $this->wmsService->bonaeraOutUpdate($id);
                    }
                }
                break;
            default:
                break;
        }
    }
}
