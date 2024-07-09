<?php

namespace App\Console\Commands;

use App\Packages\ExchangeRate;
use Illuminate\Console\Command;

class ExchangeRateCommand extends Command
{
    protected $signature   = 'save_exchange_rate';
    protected $description = '환율 조회';

    protected ExchangeRate $exchageRate;

    public function __construct(ExchangeRate $exchageRate)
    {
        parent::__construct();

        $this->exchageRate = $exchageRate;
    }

    /**
     * 환율 조회 후 저장
     * php artisan save_exchange_rate
     */
    public function handle()
    {
        $return = $this->exchageRate->getExchangeRate();
        dd($return);
    }
}
