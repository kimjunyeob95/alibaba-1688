<?php
namespace App\Console\Commands;

use App\Constants\LogConstant;
use App\Constants\WConstant;
use App\Services\Service1688Product;
use Illuminate\Console\Command;

class Save1688CollectProduct extends Command
{
    protected $signature   = 'save_1688_collect_product {--offerids=} {--type=} {--wversion=} {--collectparams=} {--params=}';
    protected $description = '1688API 제품ID로 조회 후 DB저장';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_collect_product --offerids='671048632318' --type='offerID' --collectparams='{"nCollectOption":"product","nTranslateOption":"all","yCollectOption":"none","yTranslateOption":"statusY"}'
    */
    public function handle()
    {
        $offerids      = explode(",", $this->option('offerids'));
        $type          = $this->option('type') ?? LogConstant::COLLECT_API_OFFERID;
        $wversion      = $this->option('wversion') ?? WConstant::WAPP_W1;
        $collectParams = $this->option('collectparams') ?? '{}';
        $collectParams = json_decode($collectParams, JSON_UNESCAPED_UNICODE);
        $params        = $this->option('params') ?? '{}';
        $params        = json_decode($params, JSON_UNESCAPED_UNICODE);

        if( !empty($offerids) ){
            $this->service1688Product->collectProduct($offerids, $type, $collectParams, $params);
        }
    }
}
