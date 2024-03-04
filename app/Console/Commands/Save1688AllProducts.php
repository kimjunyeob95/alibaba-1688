<?php
namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Save1688AllProducts extends Command
{
    protected $signature   = 'save_1688_all_product';
    protected $description = '1688 모든 카테고리 상품 수집';

    public function __construct()
    {
        parent::__construct();
    }
    /*
     * 실행 구문 
     * php artisan save_1688_all_product
    */
    public function handle()
    {
        $getCategoryObjs = Category::get();
        $processes = [];
        foreach ($getCategoryObjs as $getCategoryObj) {
            $categoryId = $getCategoryObj->category_id;
            $command    = "php artisan save_1688_product --categoryid={$categoryId}";
            $process    = Process::fromShellCommandline($command);
            $process->start();
            $processes[] = $process;
        }

        foreach ($processes as $process) {
            $process->wait(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    echo 'ERR > '.$buffer;
                } else {
                    echo 'OUT > '.$buffer;
                }
            });
        }
    }
}
