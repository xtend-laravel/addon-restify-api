<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Base\BaseModel;
use Symfony\Component\HttpFoundation\Response;
use Xtend\Extensions\Lunar\Core\Models\Collection;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\ProductPresenter;
use XtendLunar\Addons\RestifyApi\Restify\ProductRepository;
use XtendLunar\Addons\RestifyApi\Restify\Repository;

class ItemsCollectionGetter extends Getter
{
    public static $uriKey = 'items-collection';

    public function handle(RestifyRequest $request, BaseModel|Repository $model = null): JsonResponse|Response
    {
        $widget = $model->model();
        $widgetParams = $widget->params;
        // @todo Handle request replacements in a separate class
        $requestParams = json_decode($request->params, true);
        $widgetParams['collection_id'] = $requestParams['collectionId'] ?? $widgetParams['collection_id'];

        /** @var \Illuminate\Support\Collection $collection */
        if (is_string($widgetParams['collection_id'])) {
            $widgetParams['collection_id'] = [$widgetParams['collection_id']];
        }
        
        $collections = Collection::find($widgetParams['collection_id']);
        if ($collections->isEmpty()) {
            return response()->json([
                'data' => [],
            ]);
        }

        return $this->productsResponse($request, $collections, $widgetParams);
    }

    protected function productsResponse(RestifyRequest $request, \Illuminate\Support\Collection $collections, array $widgetParams): JsonResponse
    {
        $products = $collections->map(function (Collection $collection) use ($request, $widgetParams) {
            return $collection
                ->products()
                ->where('status', 'published')
                ->where('stock', '>', 0)
                ->limit($widgetParams['limit'])
                ->get()
                ->collect()
                ->map(
                    fn ($product) => ProductPresenter::fromData(
                        repository: ProductRepository::resolveWith($product),
                        data: $product,
                    )->transform($request),
                );
        })->flatten(1);

        return response()->json([
            'data' => $products,
        ]);
    }
}
