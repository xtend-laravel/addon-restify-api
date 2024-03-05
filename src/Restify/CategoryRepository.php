<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use Binaryk\LaravelRestify\Fields\HasMany;
use Binaryk\LaravelRestify\Fields\MorphMany;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Support\Str;
use Xtend\Extensions\Lunar\Core\Models\Collection;
use XtendLunar\Addons\RestifyApi\Restify\Actions\GetCategoryProducts;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\CategoriesTreeGetter;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\FilterGroupsGetter;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\ShopStyleCollectionGetter;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\CategoryPresenter;

class CategoryRepository extends Repository
{
    // @todo Create Category model extending Collection query categories group
    public static string $model = Collection::class;

    public static string $presenter = CategoryPresenter::class;

    public static array $excludeFields = ['_lft', '_rgt'];

    public static bool|array $public = true;

    protected static function booting(): void
    {
        $repositoryId = request()->route()->parameter('repositoryId');
        if (is_string($repositoryId)) {
            if ($repository = \Lunar\Models\Collection::query()->firstWhere('attribute_data->name->value->en', Str::headline($repositoryId))) {
                request()->route()->setParameter('repositoryId', $repository->id);
            }
        }
    }

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
            ShopStyleCollectionGetter::make(),
        ];
    }

    public function actions(RestifyRequest $request): array
    {
        return [
            GetCategoryProducts::make(),
        ];
    }
}
