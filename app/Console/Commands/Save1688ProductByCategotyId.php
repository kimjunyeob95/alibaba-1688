<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Service1688;

class Save1688ProductByCategotyId extends Command
{
    protected $signature   = 'save_1688_product_by_category_id {--categoryid=}';
    protected $description = '1688 상품 카테고리ID별 수집';

    protected Service1688 $service1688;

    public function __construct(Service1688 $service1688)
    {
        parent::__construct();

        $this->service1688 = $service1688;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_product_by_category_id
    */
    public function handle()
    {
        $categoryId = $this->option('categoryid') ?? 10166;

        $this->service1688->saveMallProductByCategotyId($categoryId);
    }
}
