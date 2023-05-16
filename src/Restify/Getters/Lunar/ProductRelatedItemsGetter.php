<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Base\BaseModel;
use Symfony\Component\HttpFoundation\Response;
use Xtend\Extensions\Lunar\Core\Models\Collection;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\ProductRelatedPresenter;
use XtendLunar\Addons\RestifyApi\Restify\ProductRepository;
use XtendLunar\Addons\RestifyApi\Restify\Repository;

class ProductRelatedItemsGetter extends Getter
{
    public static $uriKey = 'product-related-items';

    public function handle(RestifyRequest $request, BaseModel|Repository $model = null): JsonResponse|Response
    {
        /** @var \Lunar\Models\Product $product */
        $product = $model->model();
        $collection = Collection::find($product->primary_category_id);

        return response()->json([
            'data' => $collection
                ->products()
                ->where('status', 'published')
                ->limit(10)
                ->get()
                ->collect()
                ->map(
                    fn ($product) => ProductRelatedPresenter::fromData(
                        repository: ProductRepository::resolveWith($product),
                        data: $product,
                    )->transform($request),
                ),
        ]);
    }
}
