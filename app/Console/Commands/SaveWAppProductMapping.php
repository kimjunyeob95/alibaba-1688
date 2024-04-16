<?php
namespace App\Console\Commands;

use App\Services\Service1688Product;
use Illuminate\Console\Command;

class SaveWAppProductMapping extends Command
{
    protected $signature   = 'save_wapp_product_mapping';
    protected $description = 'wapp 상품 미맵핑 컬럼 업데이트';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }
    /*
     * 실행 구문 
     * php artisan save_wapp_product_mapping
    */
    public function handle()
    {
        $this->service1688Product->wAppProductMapping();
    }
}
