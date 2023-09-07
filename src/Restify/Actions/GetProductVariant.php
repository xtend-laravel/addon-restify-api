<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;

class GetProductVariant extends Action
{
    public static $uriKey = 'get-variant';

    public function handle(ActionRequest $request, Product $product): JsonResponse
    {
        $options = [
            'color' => $request->input('color'),
            'size'  => $request->input('size')
        ];

        $options = collect($options)->filter()->toArray();
        $valueIds = [];

        foreach ($options as $handle => $value) {
            $valueIds[] = $value;
        }

        $prefix = config('lunar.database.table_prefix');
        /** @var \Xtend\Extensions\Lunar\Core\Models\ProductVariant $variant */
        $variant = $product->variants()
            ->select("{$prefix}product_variants.*")
            ->join("{$prefix}product_option_value_product_variant as vov", "vov.variant_id", "=", "{$prefix}product_variants.id")
            ->join("{$prefix}product_option_values as ov", "ov.id", "=", "vov.value_id")
            ->whereIn("ov.id", $valueIds)
            ->groupBy("{$prefix}product_variants.id", "{$prefix}product_variants.product_id")
            ->having(DB::raw("count(distinct ov.id)"), count($valueIds))
            ->firstOrFail();

        $images = $variant->images->map(fn($image) => $image->getUrl('large'));

        return response()->json([
            'data' => [
                'id'     => $variant->id,
                'stock'  => $variant->stock,
                'sku'    => $variant->sku,
                'images' => !blank($images) ? $images : [$variant->getThumbnail()->getUrl('large')],
                'price'  => $variant->basePrices()->first()?->price->value,
            ]
        ]);
    }
}
