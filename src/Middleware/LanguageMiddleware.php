<?php

namespace XtendLunar\Addons\RestifyApi\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Lunar\Models\Language;

class LanguageMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('Language-Code') ?? $request->header('Accept-Language');
        if (strlen($locale) > 2) {
            $locale = substr($locale, 0, 2);
        }

        if ($language = Language::query()->where('code', strtolower($locale))->first()) {
            App::setLocale($language->code);
        }

        return $next($request);
    }
}
