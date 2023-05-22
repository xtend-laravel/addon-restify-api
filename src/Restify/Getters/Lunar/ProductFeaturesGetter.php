<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductVariant;
use XtendLunar\Features\ProductFeatures\Models\ProductFeatureValue;

class ProductFeaturesGetter extends Getter
{
    public static $uriKey = 'product-features';

    public function handle(
        $request,
        $repository
    ) {
        // @todo optimise to load relationship faster
        $productId = $repository->model()->product_id ?? $repository->model()->id;
        $product = Product::find($productId);
        /** @var \Lunar\Models\ProductVariant $variants */
        $featureValues = $product->featureValues ?? [];

        return response()->json(
            collect($featureValues)->map(fn (ProductFeatureValue $featureValue) => [
                'id' => $featureValue->id,
                'name' => $featureValue->translate('name') ?? '---',
                'position' => $featureValue->position,
                'group' => [
                    'id' => $featureValue->productFeature->id,
                    'name' => $featureValue->productFeature->translate('name') ?? '---',
                    'position' => $featureValue->productFeature->position ?? 0,
                ]
            ]),
        );
    }

    protected function getOptions(Collection|ProductVariant $variants): Collection
    {
        $options = $variants
            ->flatMap(fn (ProductVariant $variant) => $variant->values->map(fn (ProductOptionValue $value) => [
                'variant' => $variant,
                'value' => $value,
            ]))
            ->groupBy('value.product_option_id')
            ->mapWithKeys(fn ($variantValue, $key) => [
                $this->getGroupName($key) => $variantValue->keyBy('value.id')->map(
                    fn (array $item) => $this->getVariantOption($item['value'], $item['variant']),
                )->filter(fn ($item) => $item['stock'] > 0),
            ]);

        return $options;
    }

    protected function getGroupName($key): string
    {
        $group = ProductOption::find($key);

        return Str::slug($group->translate('name'));
    }

    protected function getVariantOption(ProductOptionValue $productOptionValue, ProductVariant $productVariant): array
    {
        return [
            'id' => $productOptionValue->id,
            'name' => $productOptionValue->name,
            'price' => $productOptionValue->price,
            'color' => $productOptionValue->color ?? null,
            'image' => $productVariant->images?->first()?->getUrl() ?? $productVariant->product->thumbnail?->getUrl(),
            'stock' => $productOptionValue->product_option_id !== 2 ? $productVariant->stock : 999999,
        ];
    }
}
