<?php
namespace App\Console\Commands;

use App\Models\ProductData;
use Illuminate\Console\Command;

class TestCommands extends Command
{
    protected $signature   = 'test_commands';
    protected $description = '테스트 커맨드';

    public function __construct()
    {
        parent::__construct();
    }
    /*
     * 실행 구문 
     * php artisan test_commands
    */
    public function handle()
    {
        $totalItems = ProductData::count();          // 전체 항목 수 가져오기
        $perPage    = 1000;                          // 페이지 당 항목 수
        $totalPages = ceil($totalItems / $perPage);  // 전체 페이지 수 계산

        for ($page = 1; $page <= $totalPages; $page++) {
            echo $page . "\r\n";
            $offset = ($page - 1) * $perPage;  // 이 페이지에서 첫 번째 항목의 인덱스
            $getPrdObjs = ProductData::select(["response_json"])
                    ->offset($offset)
                    ->limit($perPage)
                    ->get();
    
            foreach ($getPrdObjs as $getPrdObj) {
                $response_json = $getPrdObj->response_json;
                $response_json = json_decode($response_json);
                $quoteType = $response_json->productSaleInfo->quoteType;
                $offerId = $response_json->offerId;
                $msg = "quoteType: {$quoteType} | offerId: {$offerId}";
                debug_log($msg, "test", "test");
            }
        }
    }
}
