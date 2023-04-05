<?php

namespace XtendLunar\Addons\RestifyApi;

use Binaryk\LaravelRestify\RestifyApplicationServiceProvider;
use Binaryk\LaravelRestify\Traits\InteractsWithRestifyRepositories;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Lunar\Models\Brand;
use Lunar\Models\Cart;
use Lunar\Models\Collection;
use Lunar\Models\Order;
use Lunar\Models\Product;
use XtendLunar\Addons\RestifyApi\Policies\BrandPolicy;
use XtendLunar\Addons\RestifyApi\Policies\CartPolicy;
use XtendLunar\Addons\RestifyApi\Policies\CollectionPolicy;
use XtendLunar\Addons\RestifyApi\Policies\OrderPolicy;
use XtendLunar\Addons\RestifyApi\Policies\ProductPolicy;

class RestifyApiProvider extends RestifyApplicationServiceProvider
{
    use InteractsWithRestifyRepositories;

    /**
     * The policy mappings for restify.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Order::class => OrderPolicy::class,
        Cart::class => CartPolicy::class,
        Brand::class => BrandPolicy::class,
        Collection::class => CollectionPolicy::class,
        Product::class => ProductPolicy::class,
    ];

    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'xtend-lunar::restify-api');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'xtend-lunar::restify-api');

        $this->booting(function () {
            $this->registerPolicies();
        });

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
        $this->registerPolicies();
    }

    protected function gate(): void
    {
        Gate::define('viewRestify', function ($user = null) {
            return true;
        });
    }

    protected function registerPolicies()
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
