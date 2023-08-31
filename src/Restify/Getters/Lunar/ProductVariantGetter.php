<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Symfony\Component\HttpFoundation\Response;

class ProductVariantGetter extends Getter
{
    public static $uriKey = 'variant';

    /**
     * @param RestifyRequest $request
     * @param Model|null|Product $model
     * @return JsonResponse|Response
     */
    public function handle(RestifyRequest $request, ?Model $model = null): JsonResponse|Response
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
        $variant = $model->variants()
            ->select("{$prefix}product_variants.*")
            ->join("{$prefix}product_option_value_product_variant as vov", "vov.variant_id", "=", "{$prefix}product_variants.id")
            ->join("{$prefix}product_option_values as ov", "ov.id", "=", "vov.value_id")
            ->whereIn("ov.id", $valueIds)
            ->groupBy("{$prefix}product_variants.id", "{$prefix}product_variants.product_id")
            ->having(DB::raw("count(distinct ov.id)"), count($valueIds))
            ->firstOrFail();

        return response()->json([
            'id'     => $variant->id,
            'stock'  => $variant->stock,
            'sku'    => $variant->sku,
            'images' => $variant->images->map(fn($image) => $image->getUrl()),
            'price'  => $variant->basePrices()->first()?->price->value,
        ]);
    }
}
