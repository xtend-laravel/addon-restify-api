<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Lunar\Models\CollectionGroup;
use Symfony\Component\HttpFoundation\Response;
use XtendLunar\Addons\RestifyApi\Resources\CategoryResource;

class ShopStyleCollectionGetter extends Getter
{
    public static $uriKey = 'shop-style-collections';

    public function handle(RestifyRequest $request, ?Model $model = null): JsonResponse|Response
    {
        $styleGroup = CollectionGroup::where('handle', 'styles')->first();

        return response()->json([
            'data' => $styleGroup ? CategoryResource::collection($styleGroup->collections()->get()->toTree()) : [],
        ]);
    }
}
