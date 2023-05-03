<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Concerns;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Binaryk\LaravelRestify\Repositories\Repository;
use Illuminate\Routing\Router;

/**
 * Trait InteractsWithCustomRoutes
 *
 * @mixin Repository
 */
trait InteractsWithCustomRoutes
{
    public static function routes(Router $router, $attributes = ['prefix' => 'api/restify'], $wrap = true): void
    {
        if (!static::$routes || !static::$presenter) {
            return;
        }

        $router->group(['namespace' => '\XtendLunar\Addons\RestifyApi'], function (Router $router) {
            collect(static::$routes)->each(function ($route, $name) use ($router) {
                $router->get($name, function () use ($name) {
                    $presenter = resolve(static::$presenter, [
                        'repository' => new static,
                    ]);
                    $request = new RestifyRequest(request()->all());
                    return response($presenter->transform($request));
                })->name($name)->withoutMiddleware($route['public'] ? 'auth:sanctum' : null);
            });
        });
    }
}
