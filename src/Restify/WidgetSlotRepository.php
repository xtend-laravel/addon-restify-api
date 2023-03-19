<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use XtendLunar\Addons\RestifyApi\Restify\Presenters\WidgetSlotPresenter;
use Binaryk\LaravelRestify\Fields\BelongsToMany;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Xtend\Extensions\Lunar\Core\Models\WidgetSlot;

class WidgetSlotRepository extends Repository
{
    public static string $model = WidgetSlot::class;

    public static string $presenter = WidgetSlotPresenter::class;

    public static bool|array $public = true;

    public static function related(): array
    {
        return [
            BelongsToMany::make('widgets', WidgetRepository::class),
        ];
    }

    public static function showQuery(RestifyRequest $request, Builder|Relation $query)
    {
        // @todo Move to it's own query class
        if ($request->has('splitTesting')) {
            $splitTesting = json_decode($request->splitTesting);
            $query->where('params->split_testing->page', $splitTesting->page);
            $query->where('params->split_testing->version', $splitTesting->version);
        }

        return $query;
    }
}
