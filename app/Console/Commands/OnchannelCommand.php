<?php
namespace App\Console\Commands;

use App\Constants\MallConstant;
use App\Constants\OnchannelConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Models\ProductData;
use App\Packages\Onchannel;
use App\Services\Mall\MallApiService;
use Illuminate\Console\Command;

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
                $prdBuilder = ProductData::select([
                    "product_datas.offer_id",
                ])
                ->leftJoin("onchannel_product_logs as b", "product_datas.offer_id", "=", "b.offer_id")
                ->where("product_datas.mapping_status", ProductConstant::MAPPING_STATUS_Y)
                ->where("product_datas.status", "!=", ProductConstant::PRD_STATUS_MISS);

                $prdBuilder->where(function($query){
                    $query->where("b.regist_success", "!=", MallConstant::REGIST_SUCCESS)
                        ->orWhereNull("b.regist_success");
                });

                $objs = $prdBuilder->pluck('offer_id')->toArray();
                $params = [
                    "sendTypeList" => [OnchannelConstant::PRD_CHANNEL]
                ];
                if( !empty($objs) ){
                    $this->mallApiService->productRegist($objs, $params);
                }

                break;

            /**
             * 상품등록 커맨드
             * php artisan onchannel_command --func=productRegist --offerids=44798792934,562321147241 --sendtype=W1
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
             * php artisan easy_sell_command --func=sendModiProduct
             */
            case 'sendModiProduct':
                $this->mallApiService->sendModiProduct();
                break;

            default:
                break;
        }
    }
}
