<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Router;
use Lunar\Models\Customer;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\CustomerPresenter;

class CustomerRepository extends Repository
{
    public static string $model = Customer::class;

    public static string $presenter = CustomerPresenter::class;

    public static function routes(Router $router, $attributes = [], $wrap = true): void
    {
        $router->group(['namespace' => '\XtendLunar\Addons\RestifyApi'], function (Router $router) {
            $router->get('/account/{section?}', function (string $section = null) {
                /** @var JsonResource|string $resource */
                $resource = 'XtendLunar\Addons\RestifyApi\Resources\Customer\\'.ucfirst($section ?? 'Dashboard').'Resource';
                abort_unless(class_exists($resource), 404);

                return $resource::make(request()->all());
            })->name('account.'.($section ?? 'dashboard'));
        });
    }
}
