<?php
namespace App\Console\Commands;

use App\Constants\MallConstant;
use App\Constants\OnchannelConstant;
use App\Constants\ProductConstant;
use App\Models\ProductData;
use App\Packages\Onchannel;
use App\Services\Mall\MallApiService;
use Illuminate\Console\Command;
use Illuminate\Pagination\Paginator;

class OnchannelCommand extends Command
{
    protected $signature   = 'onchannel_command {--func=} {--offerids=} {--sendtype=}';
    protected $description = 'onchannel command';

    protected MallApiService $mallApiService;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $func  = $this->option('func');

        if( !$func ) return null;

        $this->mallApiService = new MallApiService(app(Onchannel::class));
        switch ($func) {
            /**
             * 신규 상품 등록
             * php artisan onchannel_command --func=newProductRegist
             */
            case 'newProductRegist':
                $builder = ProductData::select(["product_datas.offer_id"])
                ->leftJoin("onchannel_product_logs as b", "product_datas.offer_id", "=", "b.offer_id")
                ->where("product_datas.mapping_status", ProductConstant::MAPPING_STATUS_Y)
                ->where("product_datas.status", "!=", ProductConstant::PRD_STATUS_MISS)
                ->groupBy("product_datas.offer_id");

                $builder->where(function($query) {
                    $query->where(function($query1) {
                            $query1->where("b.regist_success", MallConstant::REGIST_ERROR);
                                //    ->where("b.message", "like", "%" . "온채널 통신 에러" . "%");
                        })
                        ->orWhereNull("b.regist_success");
                });

                $params = [
                    "sendTypeList" => [ OnchannelConstant::PRD_CHANNEL ]
                ];

                $msg = "온채널 자동 상품등록 전송 시작";
                debug_log($msg, "onchanne/autoPrdRegist", "autoPrdRegist");

                $perPage    = 900;
                $totalCount = count($builder->get());
                $totalPages = ceil($totalCount / $perPage);
                for ($page = 1; $page <= $totalPages; $page++) {
                    Paginator::currentPageResolver(function () use ($page) {
                        return $page;
                    });
                    
                    // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
                    $pagedData = $builder->paginate($perPage);
                    $results   = $pagedData->pluck('offer_id')->toArray();
                    $this->mallApiService->productRegist($results, $params);

                    // 퍼센트 계산
                    $percent = round(($page / $totalPages) * 100);

                    $msg = "온채널 자동 상품등록 전송 진행중 ({$page}/{$totalPages}) | {$percent}% 완료";
                    debug_log($msg, "onchanne/autoPrdRegist", "autoPrdRegist");
                }

                $msg = "온채널 자동 상품등록 전송 종료";
                debug_log($msg, "onchanne/autoPrdRegist", "autoPrdRegist");
                break;

            /**
             * 상품등록 커맨드
             * php artisan onchannel_command --func=productRegist --offerids=771916492405 --sendtype=30
             */
            case 'productRegist':
                $offerIds     = explode(",", $this->option('offerids'));
                $sendtypeList = explode(",", $this->option('sendtype'));
                $params       = [
                    "sendTypeList" => $sendtypeList
                ];
                if (!empty($offerIds)) {
                    $this->mallApiService->productRegist($offerIds, $params);
                }
                break;

            /**
             * 수정 된 상품 전송
             * php artisan onchannel_command --func=sendModiProduct
             */
            case 'sendModiProduct':
                $this->mallApiService->sendModiProduct();
                break;

            default:
                break;
        }
    }
}
