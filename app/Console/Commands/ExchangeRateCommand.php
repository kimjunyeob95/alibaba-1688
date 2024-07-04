<?php

namespace App\Console\Commands;

use App\Services\ExchangeRateService;
use Illuminate\Console\Command;

class ExchangeRateCommand extends Command
{
    protected $signature = 'save_exchage_rate';
    protected $description = 'get today´s exchange rate';

    protected ExchangeRateService $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        parent::__construct();

        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * 환율 조회 및 저장
     *
     * @return void
     */
    public function handle()
    {
        $return = $this->exchangeRateService->getExchangeRate();
        dump($return);
    }
}
