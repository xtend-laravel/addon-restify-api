<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\FilterGroupsGetter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Xtend\Extensions\Lunar\Core\Models\Collection;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\CollectionPresenter;

class CollectionRepository extends Repository
{
    public static string $model = Collection::class;

    public static string $presenter = CollectionPresenter::class;

    public static array $excludeFields = ['_lft', '_rgt'];

    public static bool|array $public = true;

    public function getters(RestifyRequest $request): array
    {
        return [
            FilterGroupsGetter::make(),
        ];
    }
}
