<?php
namespace App\Console\Commands;

use App\Constants\MallConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Models\ProductData;
use App\Packages\Onchannel;
use App\Services\Mall\MallApiService;
use Illuminate\Console\Command;

class OnchannelCommand extends Command
{
    protected $signature   = 'onchannel_command {--func=} {--offerids=} {--type=}';
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
                ->whereIn("product_datas.w_type", [ WConstant::WAPP_W1, WConstant::WAPP_W2 ])
                ->where("product_datas.mapping_status", ProductConstant::MAPPING_STATUS_Y)
                ->where("product_datas.status", "!=", ProductConstant::PRD_STATUS_MISS);

                $prdBuilder->where(function($query){
                    $query->where("b.regist_success", MallConstant::REGIST_FAIL)
                        ->orWhere("b.message", "온채널 통신 에러")
                        ->orWhere("b.message", "Empty options")
                        ->orWhereNull("b.regist_success");
                });

                $objs = $prdBuilder->pluck('offer_id')->toArray();                
                if( !empty($objs) ){
                    $this->mallApiService->productRegist($objs);
                }

                break;

            /**
             * 상품등록 커맨드
             * php artisan easy_sell_command --func=productRegist --offerids=44798792934,562321147241 --type=W1
             */
            case 'productRegist':
                $offerIds = explode(",", $this->option('offerids'));
                $type = $this->option('type');
                if (!empty($offerIds)) {
                    $result = $this->mallApiService->productRegist($offerIds, $type);
                    dd($result);
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
