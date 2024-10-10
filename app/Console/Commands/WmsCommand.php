<?php
namespace App\Console\Commands;

use App\Services\Wms\WmsService;
use App\Services\Wms\WmsW1;
use Illuminate\Console\Command;

class WmsCommand extends Command
{
    protected $signature   = 'wms_command {--func=} {--ids=}';
    protected $description = 'wms command';

    protected WmsService $wmsService;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $func = $this->option('func');

        if( !$func ) return null;

        $this->wmsService = new WmsService(app(WmsW1::class));
        switch ($func) {
            /**
             * 입고신청
             * php artisan wms_command --func=bonaeraInFailCreate --ids=1
             */
            case 'bonaeraInFailCreate':
                $ids = explode(",", $this->option('ids'));
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $this->wmsService->bonaeraInFailCreate($id);
                    }
                }
                break;
            /**
             * 입고정보 업데이트
             * php artisan wms_command --func=bonaeraInUpdate --ids=1
             */
            case 'bonaeraInUpdate':
                $ids = explode(",", $this->option('ids'));
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $this->wmsService->bonaeraInUpdate($id);
                    }
                }
                break;
            /**
             * 출고정보 업데이트
             * php artisan wms_command --func=bonaeraOutUpdate --ids=1
             */
            case 'bonaeraOutUpdate':
                $ids = explode(",", $this->option('ids'));
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $this->wmsService->bonaeraOutUpdate($id);
                    }
                }
                break;
            default:
                break;
        }
    }
}
