<?php
namespace App\Console\Commands;

use App\Constants\MallConstant;
use App\Packages\EasySell;
use App\Services\MallApiService;
use Illuminate\Console\Command;

class EasySellCommand extends Command
{
    protected $signature   = 'easy_sell_command {--func=} {--offerids=}';
    protected $description = 'easySell command';

    protected MallApiService $mallApiService;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $func  = $this->option('func');

        if( !$func ) return null;

        $this->mallApiService = new MallApiService(new EasySell(MallConstant::MALL_EASYSELL));
        switch ($func) {
            /**
             * 상품등록 커맨드
             * php artisan easy_sell_command --func=productRegist --offerids=44798792934,562321147241
             */
            case 'productRegist':
                $offerIds = explode(",", $this->option('offerids'));
                if (!empty($offerIds)) {
                    $result = $this->mallApiService->productRegist($offerIds);
                    dd($result);
                }
                break;

            /**
             * 온채널 매핑데이터 사용하여 이지셀 카테고리 매핑
             * php artisan easy_sell_command --func=categoryMapping
             */
            case 'categoryMapping':
                $result = $this->mallApiService->categoryMapping();
                dd($result);
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
