<?php

namespace XtendLunar\Addons\RestifyApi\Middleware;

use Closure;
use Illuminate\Http\Request;
use XtendLunar\Addons\RestifyApi\Bootstrap\Boot;

class RestifyInjector
{
    public function handle(Request $request, Closure $next)
    {
        app(Boot::class)->boot();

        return $next($request);
    }
}
