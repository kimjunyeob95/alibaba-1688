<?php
namespace App\Console\Commands;

use App\Services\Category\CategoryV1;
use Illuminate\Console\Command;

class Save1688Category extends Command
{
    protected $signature   = 'save_1688_category';
    protected $description = '1688 카테고리 수집';

    protected CategoryV1 $categoryV1;

    public function __construct(CategoryV1 $categoryV1)
    {
        parent::__construct();

        $this->categoryV1 = $categoryV1;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_category
    */
    public function handle()
    {
        $this->categoryV1->saveCategory();
    }
}
