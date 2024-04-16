<?php
namespace App\Console\Commands;

use App\Services\Service1688Category;
use Illuminate\Console\Command;

class Save1688CategoryTree extends Command
{
    protected $signature   = 'save_1688_category_tree';
    protected $description = 'categories 테이블의 데이터들을 category_trees 테이블로 정리';

    protected Service1688Category $service1688Category;

    public function __construct(Service1688Category $service1688Category)
    {
        parent::__construct();

        $this->service1688Category = $service1688Category;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_category_tree
    */
    public function handle()
    {
        $this->service1688Category->saveCategoryTree();
    }
}
