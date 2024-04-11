<?php
namespace App\Console\Commands;

use App\Constants\ProductConstant;
use App\Services\Service1688Product;
use Illuminate\Console\Command;

class SaveProductSearchData extends Command
{
    protected $signature   = 'save_product_search_data {--offerIds=} {--search_title=} {--search_type=}';
    protected $description = '상품상세 URL로 조회 요청';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }

    /*
     * 실행 구문 
     * php artisan save_product_search_data
    */
    public function handle()
    {
        $offerIds     = $this->option('offerIds') ?? "";
        $search_title = $this->option('search_title') ?? "";
        $search_type  = $this->option('search_type') ?? ProductConstant::SEARCH_TYPE_URL;
        
        if( $offerIds != "" && $search_title != "" ){
            $params = [
                "offerIds"     => explode(",", $offerIds),
                "search_title" => $search_title,
                "search_type"  => $search_type
            ];
            $this->service1688Product->saveProductSearchData($params);
        }
    }
}
