<?php

namespace XtendLunar\Addons\RestifyApi;

use Binaryk\LaravelRestify\RestifyApplicationServiceProvider;
use Illuminate\Support\Facades\Blade;
use XtendLunar\Addons\RestifyApi\Base\RestifyApi;

class RestifyApiProvider extends RestifyApplicationServiceProvider
{
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'xtend-lunar::restify-api');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'xtend-lunar::restify-api');
    }

    public function boot()
    {
        Blade::componentNamespace('XtendLunar\\Addons\\RestifyApi\\Components', 'xtend-lunar::restify-api');
        $this->overrideRestifyConfig();

        parent::boot();
    }

    protected function overrideRestifyConfig()
    {
        $config = config('restify');
        $config['repositories']['path'] = __DIR__.'/Restify';
        $config['repositories']['namespace'] = 'XtendLunar\\Addons\\RestifyApi\\Restify\\';
        config(['restify' => $config]);
    }

    /**
     * {@inheritdoc}
     */
    protected function repositories(): void
    {
        RestifyApi::repositoriesFrom(
            directory: config('restify.repositories.path', app_path('Restify')),
        );
    }
}
