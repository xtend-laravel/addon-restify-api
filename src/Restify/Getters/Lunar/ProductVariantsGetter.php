<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductVariant;

class ProductVariantsGetter extends Getter
{
    public static $uriKey = 'product-variants';

    public function handle(
        $request,
        $repository
    ) {
        // @todo optimise to load relationship faster
        $productId = $repository->model()->product_id ?? $repository->model()->id;
        $product = Product::find($productId);
        /** @var \Lunar\Models\ProductVariant $variants */
        $variants = $product->variants ?? [];

        return response()->json([
            'options' => $this->getOptions($variants),
        ]);
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
                ),
            ]);

        return $options;
    }

    protected function getGroupName($key): string
    {
        $group = ProductOption::find($key);

        return Str::slug($group->translate('name', 'en'));
    }

    protected function getVariantOption(ProductOptionValue $productOptionValue, ProductVariant $productVariant): array
    {
        return [
            'id' => $productOptionValue->id,
            'variant_id' => $productVariant->id,
            'name' => $productOptionValue->name,
            'price' => $productOptionValue->price,
            'color' => $productOptionValue->color ?? null,
            'primary_color' => $productOptionValue->primary_color ?? null,
            'secondary_color' => $productOptionValue->secondary_color ?? null,
            'tertiary_color' => $productOptionValue->tertiary_color ?? null,
            'image' => $productVariant->images?->first()?->getUrl() ?? $productVariant->product->thumbnail?->getUrl(),
            'stock' => $productVariant->stock,
            'sku' => $productVariant->sku,
        ];
    }
}
