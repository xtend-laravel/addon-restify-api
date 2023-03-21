<?php

namespace XtendLunar\Addons\RestifyApi;

use Binaryk\LaravelRestify\RestifyApplicationServiceProvider;
use Binaryk\LaravelRestify\Traits\InteractsWithRestifyRepositories;
use Illuminate\Support\Facades\Blade;

class RestifyApiProvider extends RestifyApplicationServiceProvider
{
    use InteractsWithRestifyRepositories;

    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'xtend-lunar::restify-api');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'xtend-lunar::restify-api');
        $this->loadRestifyFrom(__DIR__.'/Restify', __NAMESPACE__.'\\Restify\\');
    }

    public function boot()
    {
        Blade::componentNamespace('XtendLunar\\Addons\\RestifyApi\\Components', 'xtend-lunar::restify-api');
        parent::boot();
    }
}
