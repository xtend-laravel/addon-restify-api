<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use XtendLunar\Addons\RestifyApi\Restify\CollectionRepository;
use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\GetterRequest;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Http\JsonResponse;

class CollectionImageGetter extends Getter
{
    public static $uriKey = 'collection-image';

    public function handle(
        GetterRequest|RestifyRequest $request,
        CollectionRepository $repository
    ): JsonResponse {
        return response()->json([
            'main' => $repository->model()->thumbnail?->getUrl() ?? null,
        ]);
    }
}
