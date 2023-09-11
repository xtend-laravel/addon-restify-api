<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Collection;
use Lunar\Models\Product;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\ProductPresenter;
use XtendLunar\Addons\RestifyApi\Restify\ProductRepository;

class GetCategoryProducts extends Action
{
    public static $uriKey = 'get-products';

    public function handle(ActionRequest $request, Collection $collection): JsonResponse
    {
        return response()->json([
            'data' => $collection
                ->products()
                ->where('status', 'published')
                ->where('stock', '>', 0)
                ->limit($request->input('limit', 15))
                ->get()
                ->collect()
                ->map(fn($product) => [
                    'id' => $product->id,
                    'attributes' => ProductPresenter::fromData(repository: ProductRepository::resolveWith($product), data: $product,)->transform($request)
                ])
        ]);
    }
}
