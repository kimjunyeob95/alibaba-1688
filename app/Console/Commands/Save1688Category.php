<?php
namespace App\Console\Commands;

use App\Services\Service1688Category;
use Illuminate\Console\Command;

class Save1688Category extends Command
{
    protected $signature   = 'save_1688_category';
    protected $description = '1688API 카테고리 endPoint 조회 후 저장';

    protected Service1688Category $service1688Category;

    public function __construct(Service1688Category $service1688Category)
    {
        parent::__construct();

        $this->service1688Category = $service1688Category;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_category
    */
    public function handle()
    {
        $this->service1688Category->saveCategory();
    }
}
