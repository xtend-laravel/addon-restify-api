<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\CategoriesTreeGetter;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\FilterGroupsGetter;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\CategoryPresenter;
use Binaryk\LaravelRestify\Fields\HasMany;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Xtend\Extensions\Lunar\Core\Models\Collection;

class CategoryRepository extends Repository
{
    // @todo Create Category model extending Collection query categories group
    public static string $model = Collection::class;

    public static string $presenter = CategoryPresenter::class;

    public static array $excludeFields = ['_lft', '_rgt'];

    public static bool|array $public = true;

    public static function related(): array
    {
        return [
            HasMany::make('children', self::class), // only deep 1 level
        ];
    }

    public function getters(RestifyRequest $request): array
    {
        return [
            FilterGroupsGetter::make(),
            CategoriesTreeGetter::make(),
        ];
    }
}
