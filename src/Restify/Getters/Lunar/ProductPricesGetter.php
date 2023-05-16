<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\GetterRequest;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Product;
use XtendLunar\Addons\RestifyApi\Restify\ProductRepository;

class ProductPricesGetter extends Getter
{
    public static $uriKey = 'product-prices';

    public function handle(
        GetterRequest|RestifyRequest $request,
        ProductRepository $repository
    ): JsonResponse {
        // @todo optimise to load relationship faster
        $productId = $repository->model()->product_id ?? $repository->model()->id;
        $product = Product::find($productId);

        return response()->json([
            'basePrice' => $product->basePrice?->price?->value ?? null,
        ]);
    }
}
