<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use XtendLunar\Addons\RestifyApi\Restify\Presenters\ProductPresenter;
use XtendLunar\Addons\RestifyApi\Restify\ProductRepository;
use XtendLunar\Addons\RestifyApi\Restify\Repository;
use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Base\BaseModel;
use Symfony\Component\HttpFoundation\Response;
use Xtend\Extensions\Lunar\Core\Models\Collection;

class ItemsCollectionGetter extends Getter
{
    public static $uriKey = 'items-collection';

    public function handle(RestifyRequest $request, BaseModel|Repository $model = null): JsonResponse|Response
    {
        $widget = $model->model();
        $widgetParams = $widget->params;

        if ($widgetParams['collection_id'] === null) {
            return response()->json([
                'data' => [],
            ]);
        }

        $collection = Collection::find($widgetParams['collection_id']);
        return response()->json([
            'data' => $collection
                ->products()
                ->limit($widgetParams['limit'])
                ->get()
                ->collect()
                ->map(
                    fn($product) => ProductPresenter::fromData(
                        repository: ProductRepository::resolveWith($product),
                        data: $product,
                    )->transform($request),
                ),
        ]);
    }
}
