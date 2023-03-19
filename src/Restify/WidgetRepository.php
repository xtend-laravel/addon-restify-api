<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\WidgetPresenter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Xtend\Extensions\Lunar\Core\Models\Widget;

class WidgetRepository extends Repository
{
    public static string $model = Widget::class;

    public static string $presenter = WidgetPresenter::class;

    public static bool|array $public = true;

    public function getters(RestifyRequest $request): array
    {
        return [
            Lunar\ItemsCollectionGetter::new(),
        ];
    }
}
