<?php
namespace App\Console\Commands;

use App\Services\Service1688Product;
use Illuminate\Console\Command;

class Save1688ProductByImageId extends Command
{
    protected $signature   = 'save_1688_product_by_image_id {--imgid=}';
    protected $description = '1688API 이미지ID로 상품 수집';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_product_by_image_id
    */
    public function handle()
    {
        $imgid = $this->option('imgid') ?? "1057508044606605771";

        $this->service1688Product->saveMallProductByImageId((string)$imgid);
    }
}
