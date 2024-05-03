<?php
namespace App\Console\Commands;

use App\Constants\LogConstant;
use App\Constants\WConstant;
use App\Services\Service1688Product;
use Illuminate\Console\Command;

class Save1688CollectProduct extends Command
{
    protected $signature   = 'save_1688_collect_product {--offerids=} {--type=} {--wversion=}';
    protected $description = '1688API 제품ID로 조회 후 DB저장';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_collect_product --offerids=654362860865 --wversion=W2
    */
    public function handle()
    {
        $offerids = explode(",", $this->option('offerids'));
        $type     = $this->option('type') ?? LogConstant::COLLECT_API_OFFERID;
        $wversion = $this->option('wversion') ?? WConstant::WAPP_W1;

        if( !empty($offerids) ){
            if( $wversion == WConstant::WAPP_W1 ){
                $this->service1688Product->collectProduct($offerids, $type);
            } else if( $wversion == WConstant::WAPP_W2 ){
                $this->service1688Product->collectProductW2($offerids, $type);
            }
        }
    }
}
