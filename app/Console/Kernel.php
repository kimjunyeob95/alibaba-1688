<?php

namespace App\Console;

use App\Console\Commands\EasySellCommand;
use App\Console\Commands\MissProductReCollect;
use App\Console\Commands\OnchannelCommand;
use App\Console\Commands\Save1688AllCategory;
use App\Console\Commands\Save1688AllProducts;
use App\Console\Commands\Save1688Category;
use App\Console\Commands\Save1688CategoryMapping;
use App\Console\Commands\Save1688CategoryTree;
use App\Console\Commands\Save1688CollectProduct;
use App\Console\Commands\Save1688ProductByCategotyId;
use App\Console\Commands\Save1688ProductByImageId;
use App\Console\Commands\SaveWAppProductMapping;
use App\Console\Commands\SaveWCategory;
use App\Console\Commands\SaveWCategoryMapping;
use App\Console\Commands\TestCommands;
use App\Console\Commands\UpdateAttribute;
use App\Console\Commands\UpdateForbiddenWord;
use App\Constants\WConstant;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        TestCommands::class,
        Save1688AllCategory::class,
        Save1688Category::class,
        Save1688CategoryMapping::class,
        Save1688CategoryTree::class,
        SaveWCategory::class,
        SaveWCategoryMapping::class,
        Save1688ProductByCategotyId::class,
        Save1688ProductByImageId::class,
        Save1688AllProducts::class,
        /** 제품ID로 수집 후 DB저장 */
        Save1688CollectProduct::class,
        /** wapp 상품 미맵핑 컬럼 업데이트 */
        SaveWAppProductMapping::class,
        /** 이지셀 */
        EasySellCommand::class,
        /** 온채널 */
        OnchannelCommand::class,
        /** 정보부족 상품 재수집 */
        MissProductReCollect::class,
        /** 금칙어 사전 적용 */
        UpdateForbiddenWord::class,
        /** 정보고시 적용 */
        UpdateAttribute::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        if (app()->environment('production')) {

            /** WApp */
            $schedule->command("miss_product_re_collect --wversion=". WConstant::WAPP_W1)->cron("0 */6 * * *")->description("정보부족 W1 상품 재수집")->withoutOverlapping()->runInBackground();
            $schedule->command("miss_product_re_collect --wversion=". WConstant::WAPP_W2)->cron("0 */6 * * *")->description("정보부족 W2 상품 재수집")->withoutOverlapping()->runInBackground();

            $schedule->command("update_forbidden_word")->cron("0 0 * * *")->description("금칙어 사전 적용")->withoutOverlapping()->runInBackground();

            /** 이지셀 */
            $schedule->command("easy_sell_command --func=sendModiProduct")->cron("*/5 * * * *")->description("이지셀 수정 된 상품 전송")->withoutOverlapping()->runInBackground();

            /** 온채널 신규 상품 등록 */
            $schedule->command("onchannel_command --func=newProductRegist")->cron("0 9,18 * * *")->description("온채널 신규 상품 등록")->withoutOverlapping()->runInBackground();
        }
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
