<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use XtendLunar\Addons\RestifyApi\Restify\Concerns\InteractsWithCustomRoutes;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\FilterGroupsGetter;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\ProductSaleItemsPresenter;

class ProductSaleItemsRepository extends Repository
{
    use InteractsWithCustomRoutes;

    public static array $routes = [
        'sales' => [
            'prefix' => 'api/restify',
            'public' => true,
        ],
    ];

    public static string $presenter = ProductSaleItemsPresenter::class;

    public function getters(RestifyRequest $request): array
    {
        return [
            FilterGroupsGetter::make(),
        ];
    }
}
