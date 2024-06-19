<?php
namespace App\Console\Commands;

use App\Packages\JwtPackage;
use App\Packages\S3;
use App\Services\GenuioService;
use Illuminate\Console\Command;

class GenuioCommand extends Command
{
    protected $signature   = 'genuio_command {--func=} {--offerids=}';
    protected $description = 'genuio command';

    protected GenuioService $genuioService;

    public function __construct()
    {
        parent::__construct();
        $this->genuioService = new GenuioService(app(JwtPackage::class), app(S3::class));
    }

    public function handle()
    {
        $func = $this->option('func');
        if( !$func ) return null;

        switch ($func) {
            /**
             * 번역 요청
             * php artisan genuio_command --func=imgTransRequest --offerids=
             */
            case 'imgTransRequest':
                $offerIds = explode(",", $this->option('offerids'));
                if (!empty($offerIds)) {
                    $this->genuioService->imgTransRequest($offerIds);
                }
                break;

                break;

            default:
                break;
        }
    }
}
