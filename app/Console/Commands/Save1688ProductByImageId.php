<?php
namespace App\Console\Commands;

use App\Services\Product\ProductV1;
use Illuminate\Console\Command;

class Save1688ProductByImageId extends Command
{
    protected $signature   = 'save_1688_product_by_image_id {--imgid=}';
    protected $description = '1688 상품 카테고리ID별 수집';

    protected ProductV1 $productV1;

    public function __construct(ProductV1 $productV1)
    {
        parent::__construct();

        $this->productV1 = $productV1;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_product_by_image_id
    */
    public function handle()
    {
        $imgid = $this->option('imgid') ?? 1057508044606605771;

        $this->productV1->saveMallProductByImageId($imgid);
    }
}
