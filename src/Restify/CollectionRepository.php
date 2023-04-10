<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use Binaryk\LaravelRestify\Fields\HasMany;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Xtend\Extensions\Lunar\Core\Models\Collection;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\CollectionPresenter;

class CollectionRepository extends Repository
{
    public static string $model = Collection::class;

    public static string $presenter = CollectionPresenter::class;

    public static array $excludeFields = ['_lft', '_rgt'];

    public static bool|array $public = true;

    public static function related(): array
    {
        return [
            HasMany::make('products', ProductRepository::class),
            HasMany::make('children', CollectionRepository::class),
        ];
    }

    public function getters(RestifyRequest $request): array
    {
        return [
            Lunar\CollectionImageGetter::new()->onlyOnShow(),
        ];
    }
}
