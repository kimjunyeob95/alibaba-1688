<?php
namespace App\Console\Commands;

use App\Services\Service1688Product;
use Illuminate\Console\Command;

class Save1688ProductImageQuery extends Command
{
    protected $signature   = 'save_1688_product_image_query {--imageIds=} {--sort=}';
    protected $description = '1688API imageQueryAPI로 상품 수집';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }

    /*
     * 실행 구문 
     * php artisan save_1688_product_image_query
    */
    public function handle()
    {
        $imageIds = $this->option('imageIds', "");
        $sort    = $this->option('sort') ?? "";
        
        if( $imageIds ){
            $params = [
                "imageIds" => explode(",", $imageIds),
                "sort"     => $sort,
                "page"     => 1,
                "pageSize" => 50,
            ];
            $this->service1688Product->saveImageQuery($params);
        }
    }
}
