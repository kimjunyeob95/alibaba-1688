<?php
namespace App\Console\Commands;

use App\Services\Product\ProductV1;
use Illuminate\Console\Command;

class Save1688ProductKeywordQuery extends Command
{
    protected $signature   = 'save_1688_product_keyword_query {--search_cls=} {--keyword=} {--sort=}';
    protected $description = '1688 상품 keywordQueryAPI로 수집';

    protected ProductV1 $productV1;

    public function __construct(ProductV1 $productV1)
    {
        parent::__construct();

        $this->productV1 = $productV1;
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
        
        $params = [
            "search_cls" => $search_cls,
            "keyword"    => $keyword,
            "sort"       => $sort,
            "page"       => 1,
            "pageSize"   => 50,
        ];
        $this->productV1->saveKeywordQuery($params);
    }
}
