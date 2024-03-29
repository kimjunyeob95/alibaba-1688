<?php
namespace App\Console\Commands;

use App\Services\Service1688Product;
use Illuminate\Console\Command;

class Save1688CategoryMapping extends Command
{
    protected $signature   = 'save_1688_category_mapping';
    protected $description = 'categories 테이블의 데이터들을 category_mappings 테이블로 정리';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_category_mapping
    */
    public function handle()
    {
        $this->service1688Product->saveCategoryMapping();
    }
}
