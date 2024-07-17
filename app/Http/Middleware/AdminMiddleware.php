<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $exceptRoute = $request->getRequestUri();
        $allowRoutes = ["/wapp/admin/login", "/wapp/admin/signIn"];

        if( !in_array($exceptRoute, $allowRoutes) ){
            if (!session()->has('adminInfo') || !session('adminInfo')) {
                return redirect()->route('wapp.admin.login');
            }
        }

        if( $exceptRoute == "/wapp/admin/login" && ( session()->has('adminInfo') && session('adminInfo') )) {
            return redirect()->route('product.queryProductDetail');
        }

        return $next($request);
    }
}
