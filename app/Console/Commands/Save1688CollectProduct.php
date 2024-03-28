<?php
namespace App\Console\Commands;

use App\Services\Product\ProductV1;
use Illuminate\Console\Command;

class Save1688CollectProduct extends Command
{
    protected $signature   = 'save_1688_collect_product {--offerids=}';
    protected $description = '1688 상품 제품ID별 수집';

    protected ProductV1 $productV1;

    public function __construct(ProductV1 $productV1)
    {
        parent::__construct();

        $this->productV1 = $productV1;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_collect_product
    */
    public function handle()
    {
        $offerids = explode(",", $this->option('offerids'));
        
        if( !empty($offerids) ){
            $this->productV1->collectProduct($offerids);
        }
    }
}
