<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Facades\Discounts;
use Lunar\Models\Cart;

class ApplyDiscountCartAction extends Action
{
    public function handle(ActionRequest $request, Cart $models): JsonResponse
    {
        $cart = $models;

        if (!Discounts::validateCoupon($request->discountCode)) {
            return data([
                'status' => 'invalid_coupon',
                'message' => 'The coupon code is invalid',
            ]);
        }

        $cart->update([
            'coupon_code' => $request->discountCode,
        ]);

        $cart->refresh()->calculate();

        return data([
            'status' => 'valid_coupon',
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
