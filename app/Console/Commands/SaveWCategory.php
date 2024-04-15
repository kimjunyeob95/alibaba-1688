<?php
namespace App\Console\Commands;

use App\Services\Service1688Product;
use Illuminate\Console\Command;

class SaveWCategory extends Command
{
    protected $signature   = 'save_w_category';
    protected $description = 'w_categories 카테고리 테이블로 insert';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }
    /*
     * 실행 구문 
     * php artisan save_w_category
    */
    public function handle()
    {
        $this->service1688Product->saveWCategory();
    }
}
