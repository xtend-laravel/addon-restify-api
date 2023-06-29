<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Models\CollectionGroup;
use Symfony\Component\HttpFoundation\Response;
use Xtend\Extensions\Lunar\Core\Models\Collection;
use XtendLunar\Addons\RestifyApi\Resources\CategoryResource;

class CategoriesTreeGetter extends Getter
{
    public static $uriKey = 'tree';

    public function handle(RestifyRequest $request, Collection $model = null): JsonResponse|Response
    {
        $categoryHandle = CollectionGroup::query()->where('handle', 'categories')->sole();
        return response()->json([
            'data' => $model
                ? CategoryResource::make($model)
                : CategoryResource::collection(
                    resource: Collection::query()
                        ->where('collection_group_id', $categoryHandle->id)
                        ->whereNull('parent_id')
                        ->get(),
                ),
        ]);
    }
}
