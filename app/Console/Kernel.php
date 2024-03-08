<?php

namespace App\Console;

use App\Console\Commands\Save1688AllProducts;
use App\Console\Commands\Save1688Category;
use App\Console\Commands\Save1688CategoryMapping;
use App\Console\Commands\Save1688ProductByCategotyId;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * cli 명령어 모음
 * 
 * 1. 세션과 상관없이 백그라운드에서 실행
 * nohup php artisan save_1688_product_by_category_id --categoryid=1037021 > /dev/null 2>&1 &
 * 
 */

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Save1688Category::class,
        Save1688CategoryMapping::class,
        Save1688ProductByCategotyId::class,
        Save1688AllProducts::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    public function handle($input, $output = null)
    {
        set_time_limit(0); // 시간 제한을 무제한으로 설정

        parent::handle($input, $output);
    }
}
