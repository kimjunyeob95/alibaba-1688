<?php
namespace App\Console\Commands;

use App\Packages\EasySell;
use App\Services\MallApiService;
use Illuminate\Console\Command;

class EasySellCommand extends Command
{
    protected $signature   = 'easy_sell_command {--func=}';
    protected $description = 'easySell command';

    protected MallApiService $mallApiService;

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * 실행 구문 
     * php artisan easy_sell_command --func=productRegist
    */
    public function handle()
    {
        $func  = $this->option('func');

        if( !$func ) return null;

        $this->mallApiService = new MallApiService(new EasySell());
        switch ($func) {
            case 'productRegist':
            default:
                $result = $this->mallApiService->productRegist();
                dd($result);
                break;
        }
    }
}
