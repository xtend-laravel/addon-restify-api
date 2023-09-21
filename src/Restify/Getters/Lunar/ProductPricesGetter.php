<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\GetterRequest;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Base\Purchasable;
use Lunar\DiscountTypes\AmountOff;
use Lunar\Facades\Pricing;
use Lunar\Models\Discount;
use Lunar\Models\Product;
use Spatie\Blink\Blink;
use Spatie\LaravelBlink\BlinkFacade;
use XtendLunar\Addons\RestifyApi\Restify\ProductRepository;

class ProductPricesGetter extends Getter
{
    public static $uriKey = 'product-prices';

    public function handle(
        GetterRequest|RestifyRequest $request,
        ProductRepository            $repository
    ): JsonResponse
    {
        // @todo optimise to load relationship faster
        $productId = $repository->model()->product_id ?? $repository->model()->id;
        $product = Product::find($productId);
        $pricing = Pricing::for($product->variants->first())->get();

        $discounts = $this->findGlobalDiscounts();
        $basePrice = $product->basePrice?->price?->value ?? null;
        $discountedPrice = $basePrice;

        foreach ($discounts as $discount) {
            $discountedPrice = $this->applyDiscount($discount, $product, $basePrice);
        }

        return response()->json([
            'basePrice'       => $basePrice,
            'discountedPrice' => $discountedPrice
        ]);
    }

    protected function applyDiscount(Discount $discount, Product $product, int $basePrice): int
    {
        if ($this->isEligible($discount, $product)) {
            $data = $discount->data;

            if ($data['fixed_value']) {
                $basePrice = $basePrice - $data['fixed_values'][$product->currency->code];
                return $basePrice;
            }

            $basePrice = $basePrice - ($basePrice * $data['percentage'] / 100);
            return $basePrice;
        }
    }

    protected function isEligible(Discount $discount, Product $product): bool
    {
        $collectionIds = $discount->collections->pluck('id');
        $brandIds = $discount->brands->pluck('id');
        $productIds = $discount->purchasableLimitations
            ->reject(fn($limitation) => !$limitation->purchasable)
            ->map(fn($limitation) => get_class($limitation->purchasable) . '::' . $limitation->purchasable->id);

        if (!$collectionIds->count() && !$brandIds->count() && !$productIds->count()) {
            return true;
        }

        if ($collectionIds->count() && $product->collections->pluck('id')->intersect($collectionIds)->count()) {
            return true;
        }

        if ($brandIds->count() && $brandIds->contains($product->brand_id)) {
            return true;
        }

        if ($productIds->count() && $productIds->contains(get_class($product) . '::' . $product->id)) {
            return true;
        }

        return false;
    }

    /**
     * @return Discount[]
     */
    protected function findGlobalDiscounts(): array
    {
        return BlinkFacade::once('global_discounts', function () {
            $discounts = Discount::active()->usable()->whereNull('coupon')
                ->whereType(AmountOff::class)
                ->orderBy('priority', 'desc')
                ->get();

            $applicableDiscounts = [];

            foreach ($discounts as $discount) {
                $applicableDiscounts[] = $discount;

                if ($discount->stop) {
                    break;
                }
            }

            return $applicableDiscounts;
        });
    }
}
