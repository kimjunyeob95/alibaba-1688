<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WhiteListIpMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $exceptRoute = "/" . $request->path();
        $allowRoutes = ["/api/w/1688/test"];

        if( !in_array($exceptRoute, $allowRoutes) ){
            // .env에서 허용된 IP 목록을 가져옵니다.
            $allowedIps = explode(',', env('WHITELIST_IPS'));

            // $msg = "ip: ". $request->ip();
            // debug_log($msg, "wapp/checkIp", "checkIp");

            // 요청 IP가 허용 목록에 없으면 접근을 거부합니다.
            if (!in_array($request->ip(), $allowedIps)) {
                abort(403, 'No White List Ip');
            }
        }

        return $next($request);
    }
}
