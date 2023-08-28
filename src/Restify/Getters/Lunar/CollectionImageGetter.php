<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\GetterRequest;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use XtendLunar\Addons\RestifyApi\Restify\CollectionRepository;

class CollectionImageGetter extends Getter
{
    public static $uriKey = 'collection-image';

    public function handle(
        GetterRequest|RestifyRequest $request,
        CollectionRepository $repository
    ): JsonResponse {
        return response()->json([
            'main' => $repository->model()->thumbnail?->getUrl() ?? null,
            'gallery' => $repository->model()->getMedia('images')->sort(function (Media $media) {
                return $media->getCustomProperty('primary') ? 0 : 1;
            })->map->getUrl() ?? [],
        ]);
    }
}
