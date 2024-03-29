<?php
namespace App\Console\Commands;

use App\Services\Service1688Product;
use Illuminate\Console\Command;

class Save1688ProductKeywordQuery extends Command
{
    protected $signature   = 'save_1688_product_keyword_query {--search_cls=} {--keyword=} {--sort=}';
    protected $description = '1688API keywordQueryAPI로 상품 수집';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }

    /*
     * 실행 구문 
     * php artisan save_1688_product_keyword_query
    */
    public function handle()
    {
        $search_cls = $this->option('search_cls') ?? "";
        $keyword    = $this->option('keyword') ?? "";
        $sort       = $this->option('sort') ?? "";
        
        if( $keyword != "" ){
            $params = [
                "search_cls" => $search_cls,
                "keyword"    => $keyword,
                "sort"       => $sort,
                "page"       => 1,
                "pageSize"   => 50,
            ];
            $this->service1688Product->saveKeywordQuery($params);
        }
    }
}
