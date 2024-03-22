<?php
namespace App\Console\Commands;

use App\Services\Category\CategoryV1;
use Illuminate\Console\Command;
use App\Services\Service1688;

class Save1688CategoryMapping extends Command
{
    protected $signature   = 'save_1688_category_mapping';
    protected $description = '수집 된 1688 카테고리 정규화';

    protected CategoryV1 $categoryV1;

    public function __construct(CategoryV1 $categoryV1)
    {
        parent::__construct();

        $this->categoryV1 = $categoryV1;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_category_mapping
    */
    public function handle()
    {
        $this->categoryV1->saveCategoryMapping();
    }
}
