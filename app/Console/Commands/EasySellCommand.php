<?php
namespace App\Console\Commands;

use App\Services\Mall\MallApiService;
use App\Packages\EasySell;
use App\Services\Mall\MallCategoryApiService;
use Illuminate\Console\Command;

class EasySellCommand extends Command
{
    protected $signature   = 'easy_sell_command {--func=} {--offerids=} {--type=}';
    protected $description = 'easySell command';

    protected MallApiService $mallApiService;
    protected MallCategoryApiService $mallCategoryApiService;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $func  = $this->option('func');

        if( !$func ) return null;

        $this->mallApiService         = new MallApiService(app(EasySell::class));
        $this->mallCategoryApiService = new MallCategoryApiService(app(EasySell::class));

        switch ($func) {
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
             * 온채널 매핑데이터 사용하여 이지셀 카테고리 매핑
             * php artisan easy_sell_command --func=categoryMapping
             */
            case 'categoryMapping':
                $result = $this->mallCategoryApiService->categoryMapping();
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
