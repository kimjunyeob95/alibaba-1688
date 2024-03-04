<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Service1688;

class Save1688Product extends Command
{
    protected $signature   = 'save_1688_product {--categoryid=}';
    protected $description = '1688 상품 수집';

    protected Service1688 $service1688;

    public function __construct(Service1688 $service1688)
    {
        parent::__construct();

        $this->service1688 = $service1688;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_product
    */
    public function handle()
    {
        $categoryId = $this->option('categoryid', 1038378);

        $this->service1688->saveMallProduct($categoryId);
    }
}
