<?php

namespace XtendLunar\Addons\RestifyApi;

use CodeLabX\XtendLaravel\Base\XtendAddonProvider;
use Illuminate\Support\Facades\Blade;

class RestifyApiProvider extends XtendAddonProvider
{
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'xtend-lunar::restify-api');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'xtend-lunar::restify-api');
    }

    public function boot()
    {
        Blade::componentNamespace('XtendLunar\\Addons\\RestifyApi\\Components', 'xtend-lunar::restify-api');
    }
}
