<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use XtendLunar\Addons\RestifyApi\Restify\Concerns\InteractsWithCustomRoutes;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\FilterGroupsGetter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\ProductNewItemsPresenter;

class ProductNewItemsRepository extends Repository
{
    use InteractsWithCustomRoutes;

    public static array $routes = [
        'new-products' => [
            'prefix' => 'api/restify',
            'public' => true,
        ],
    ];

    public static string $presenter = ProductNewItemsPresenter::class;

    public function getters(RestifyRequest $request): array
    {
        return [
            FilterGroupsGetter::make(),
        ];
    }
}
