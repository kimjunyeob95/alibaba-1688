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

    /*
     * 실행 구문 
     * php artisan easy_sell_command --func=productRegist --offerids=731918432151,721717418135
    */
    public function handle()
    {
        $func  = $this->option('func');

        if( !$func ) return null;

        $this->mallApiService = new MallApiService(new EasySell(MallConstant::MALL_EASYSELL));
        switch ($func) {
            case 'productRegist':
            default:
                $offerIds = explode(",", $this->option('offerids'));
                $result = $this->mallApiService->productRegist($offerIds);
                dd($result);
                break;
        }
    }
}
