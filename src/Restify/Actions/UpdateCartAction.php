<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Lunar\Base\Purchasable;
use Lunar\Models\Cart;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductVariant;

class UpdateCartAction extends Action
{
    public function handle(ActionRequest $request, Cart $cart): JsonResponse
    {
        $purchasable = $this->getPurchasable($request->product);

        $cart->add(
            purchasable: $purchasable,
            quantity: $request->product['quantity'] ?? 1,
        )->calculate();

        // @todo return any validation stock errors or any other errors when adding lines to cart
        return data($cart->lines->groupBy('purchasable_id')->get($purchasable->id)->flatMap(function ($line) {
            return [
                'id'          => $line->purchasable->product_id,
                'purchasable' => $line->purchasable,
                'quantity'    => $line->quantity,
                'total'       => $line->total->value,
            ];
        }));
    }

    protected function getPurchasable(array $product): Purchasable|Model
    {
        $variants = array_filter($product['variants'] ?? []);
        // when the product has no variants, we can just return the base variant
        if (blank($variants)) {
            return ProductVariant::query()->where([
                'product_id' => $product['id'],
                'base'       => true,
            ])->sole();
        }

        return ProductVariant::query()
            ->where(['product_id' => $product['id'], 'base' => false])
            ->get()
            ->first(
                fn(ProductVariant $variant) => $variant->values->pluck('id')->diff(array_values($product['variants']))->isEmpty()
            );
    }
}
