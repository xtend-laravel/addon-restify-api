<?php

namespace XtendLunar\Addons\RestifyApi;

use Binaryk\LaravelRestify\RestifyApplicationServiceProvider;
use Binaryk\LaravelRestify\Traits\InteractsWithRestifyRepositories;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

class RestifyApiProvider extends RestifyApplicationServiceProvider
{
    use InteractsWithRestifyRepositories;

    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'xtend-lunar::restify-api');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'xtend-lunar::restify-api');
        $this->loadRestifyFrom(__DIR__.'/Restify', __NAMESPACE__.'\\Restify\\');
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__.'/../routes/api.php');
    }

    public function boot()
    {
        Blade::componentNamespace('XtendLunar\\Addons\\RestifyApi\\Components', 'xtend-lunar::restify-api');
        parent::boot();
    }
}
