<?php

namespace XtendLunar\Addons\RestifyApi;

use Binaryk\LaravelRestify\Bootstrap\RoutesBoot;
use Binaryk\LaravelRestify\RestifyApplicationServiceProvider;
use Binaryk\LaravelRestify\Traits\InteractsWithRestifyRepositories;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Lunar\Models\Address;
use Lunar\Models\Brand;
use Lunar\Models\Cart;
use Lunar\Models\Collection;
use Lunar\Models\Customer;
use Lunar\Models\Order;
use Lunar\Models\Product;
use XtendLunar\Addons\RestifyApi\Middleware\LanguageMiddleware;
use XtendLunar\Addons\RestifyApi\Middleware\RestifyInjector;
use XtendLunar\Addons\RestifyApi\Policies\AddressPolicy;
use XtendLunar\Addons\RestifyApi\Policies\BrandPolicy;
use XtendLunar\Addons\RestifyApi\Policies\CartPolicy;
use XtendLunar\Addons\RestifyApi\Policies\CollectionPolicy;
use XtendLunar\Addons\RestifyApi\Policies\CustomerPolicy;
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
        Address::class => AddressPolicy::class,
        Brand::class => BrandPolicy::class,
        Collection::class => CollectionPolicy::class,
        Product::class => ProductPolicy::class,
        Customer::class => CustomerPolicy::class,
    ];

    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'xtend-lunar::restify-api');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'xtend-lunar::restify-api');
        $this->registerLanguageMiddleware();

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

    protected function routes(): void
    {
        /**
         * @var Kernel $kernel
         */
        $kernel = $this->app->make(Kernel::class);

        $kernel->pushMiddleware(RestifyInjector::class);

        // List routes when running artisan route:list
        if (App::runningInConsole() && ! App::runningUnitTests()) {
            app(RoutesBoot::class)->boot();
        }
    }

    protected function registerPolicies()
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    protected function registerLanguageMiddleware(): void
    {
        $restifyMiddleware = config('restify.middleware');
        config(['restify.middleware' => array_merge($restifyMiddleware, [
            LanguageMiddleware::class,
        ])]);
    }
}
