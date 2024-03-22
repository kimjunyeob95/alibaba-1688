<?php
namespace App\Console\Commands;

use App\Services\Product\ProductV1;
use Illuminate\Console\Command;

class Save1688ProductByCategotyId extends Command
{
    protected $signature   = 'save_1688_product_by_category_id {--categoryid=}';
    protected $description = '1688 상품 카테고리ID별 수집';

    protected ProductV1 $productV1;

    public function __construct(ProductV1 $productV1)
    {
        parent::__construct();

        $this->productV1 = $productV1;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_product_by_category_id
    */
    public function handle()
    {
        $categoryId = $this->option('categoryid') ?? 10166;

        $this->productV1->saveMallProductByCategotyId($categoryId);
    }
}
