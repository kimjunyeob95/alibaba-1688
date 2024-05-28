<?php
namespace App\Console\Commands;

use App\Constants\ExceptConstant;
use App\Constants\ForbiddenWordConstant;
use App\Models\ForbiddenWordData;
use App\Models\ProductData;
use App\Models\ProductExceptData;
use App\Models\ProductForbiddenData;
use App\Services\Service1688Product;
use Illuminate\Console\Command;
use Illuminate\Pagination\Paginator;

class UpdateAttribute extends Command
{
    protected $signature   = 'update_attribute';
    protected $description = '정보고시 적용';

    protected Service1688Product $service1688Product;

    public function __construct(Service1688Product $service1688Product)
    {
        parent::__construct();

        $this->service1688Product = $service1688Product;
    }
    /*
     * 실행 구문 
     * php artisan update_attribute
    */
    public function handle()
    {
        // 1. 정보고시 제외 처리
        ProductExceptData::where([
            "except_type" => ExceptConstant::EXCEPT_NOTICE
        ]);

        $perPage = 900;

        $builder = ProductData::query();

        $totalCount = $builder->count();
        $totalPages = ceil($totalCount / $perPage);
        
        $deletePrdForbiddenWords  = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_DELETE)->get();
        $replacePrdForbiddenWords = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_REPLACE)->get();

    }
}
