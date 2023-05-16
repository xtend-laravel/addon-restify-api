<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Lunar\Models\Brand;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\CategoriesTreeGetter;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\FilterGroupsGetter;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\BrandPresenter;

class BrandRepository extends Repository
{
    public static string $model = Brand::class;

    public static string $presenter = BrandPresenter::class;

    public static bool|array $public = true;

    public static int $defaultPerPage = 500;

    public function getters(RestifyRequest $request): array
    {
        return [
            FilterGroupsGetter::make(),
            CategoriesTreeGetter::make(),
        ];
    }
}
