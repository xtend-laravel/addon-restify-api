<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Cart;

class ApplyDiscountCartAction extends Action
{
    public function handle(ActionRequest $request, Cart $models): JsonResponse
    {
        $cart = $models;

        $cart->update([
            'coupon_code' => $request->discountCode,
        ]);

        $cart->refresh()->calculate();

        return data([
            'cart' => [
                'id' => $cart->id,
                'totals' => [
                    'sub_total' => $cart->subTotal->value,
                    'sub_total_discounted' => $cart->subTotalDiscounted->value,
                    'discount_total' => $cart->discountTotal?->value,
                    'shipping_total' => $cart->shippingSubTotal?->value,
                    'tax_total' => $cart->taxTotal->value,
                    'total' => $cart->total->value,
                ],
            ],
        ]);
    }
}
