<?php

namespace App\Http\Middleware;

use App\Models\ExchangeRateHistory;
use Closure;

class ConfigMiddleware
{
    public function handle($request, Closure $next)
    {
        /** 최신 환율 config 등록 */
        $latestObj = ExchangeRateHistory::orderBy("date", "desc")->first();
        if ($latestObj != null) {
            config(['1688_EXCHANGE_RATE' => $latestObj->exchange_rate]);
        }

        return $next($request);
    }
}
