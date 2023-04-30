<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use Illuminate\Routing\Router;
use Lunar\Models\Customer;
use XtendLunar\Addons\RestifyApi\Controllers\AccountController;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\CustomerPresenter;

class CustomerRepository extends Repository
{
    public static string $model = Customer::class;

    public static string $presenter = CustomerPresenter::class;

    public static function routes(Router $router, $attributes = [], $wrap = true)
    {
        $router->group(['namespace' => '\XtendLunar\Addons\RestifyApi'], function (Router $router) {
            $router->get('/account/{section?}', AccountController::class)->name('account');
        });
    }
}
