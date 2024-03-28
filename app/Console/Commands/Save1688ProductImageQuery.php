<?php
namespace App\Console\Commands;

use App\Services\Product\ProductV1;
use Illuminate\Console\Command;

class Save1688ProductImageQuery extends Command
{
    protected $signature   = 'save_1688_product_image_query {--imageid=} {--sort=}';
    protected $description = '1688 상품 imageQueryAPI로 수집';

    protected ProductV1 $productV1;

    public function __construct(ProductV1 $productV1)
    {
        parent::__construct();

        $this->productV1 = $productV1;
    }
    /*
     * 실행 구문 
     * php artisan save_1688_product_image_query
    */
    public function handle()
    {
        $imageId = $this->option('imageid');
        $sort    = $this->option('sort') ?? "";
        
        if( $imageId ){
            $params = [
                "imageId"  => $imageId,
                "sort"     => $sort,
                "page"     => 1,
                "pageSize" => 50,
            ];
            $this->productV1->saveImageQuery($params);
        }
    }
}
