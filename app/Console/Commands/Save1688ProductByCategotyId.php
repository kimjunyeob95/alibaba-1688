<?php
namespace App\Console\Commands;

use App\Services\Service1688Product;
use Illuminate\Console\Command;

class Save1688ProductByCategotyId extends Command
{
    protected $signature   = 'save_1688_product_by_category_id {--categoryid=}';
    protected $description = '1688API 카테고리ID별 상품수집';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_product_by_category_id
    */
    public function handle()
    {
        $categoryId = $this->option('categoryid') ?? 10166;

        $this->service1688Product->saveMallProductByCategotyId((int)$categoryId);
    }
}
