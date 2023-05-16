<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\GetterRequest;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Product;
use XtendLunar\Addons\RestifyApi\Restify\ProductRepository;

class ProductImagesGetter extends Getter
{
    public static $uriKey = 'product-images';

    public function handle(
        GetterRequest|RestifyRequest $request,
        ProductRepository $repository
    ): JsonResponse {
        // @todo optimise to load relationship faster
        $productId = $repository->model()->product_id ?? $repository->model()->id;
        $product = Product::find($productId);
        // @todo optimise images size later + hack for jacquesloup until sync new catalog
        $size = str_contains($product->thumbnail?->getUrl(), 'fra1.digitaloceanspaces') ? 'medium' : '';

        return response()->json([
            'thumbnail' => $product->thumbnail?->getUrl($size) ?? null,
            'gallery' => $product->images->map(fn ($image) => $image->getUrl($size)),
        ]);
    }
}
