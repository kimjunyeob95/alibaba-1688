<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Service1688;

class Save1688CategoryMapping extends Command
{
    protected $signature   = 'save_1688_category_mapping';
    protected $description = '수집 된 1688 카테고리 정규화';

    protected Service1688 $service1688;

    public function __construct(Service1688 $service1688)
    {
        parent::__construct();

        $this->service1688 = $service1688;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_category_mapping
    */
    public function handle()
    {
        $this->service1688->saveCategoryMapping();
    }
}
