<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
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

        $selectedOptionType = $request->input('selectedOptionType');

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
                'availableVariants' => $selectedOptionType ? $this->getAvailableVariantIds($product, $selectedOptionType, $options) : [],
            ],
        ]);
    }

    protected function getAvailableVariantIds(Product $product, string $selectedOptionType, array $options): array
    {
        $selectedOption = $options[$selectedOptionType] ?? null;
        if (!$selectedOption) {
            return [];
        }

        return $product->variants()
            ->whereHas('values', function ($query) use ($selectedOption) {
                $query->where('value_id', $selectedOption);
            })
            ->get()
            ->filter(fn (ProductVariant $variant) => $variant->stock > 0)
            ->flatMap(fn (ProductVariant $variant) => $variant->values->map(fn (ProductOptionValue $value) => $value->pivot->value_id))
            ->unique()
            ->values()
            ->toArray();
    }
}
