<?php
namespace App\Console\Commands;

use App\Services\Service1688Product;
use Illuminate\Console\Command;

class Save1688Category extends Command
{
    protected $signature   = 'save_1688_category';
    protected $description = '1688API 카테고리 endPoint 조회 후 저장';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_category
    */
    public function handle()
    {
        $this->service1688Product->saveCategory();
    }
}
