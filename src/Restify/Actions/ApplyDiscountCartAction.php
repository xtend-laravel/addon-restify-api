<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Illuminate\Http\JsonResponse;
use Lunar\DiscountTypes\AmountOff;
use Lunar\Facades\Discounts;
use Lunar\Models\Cart;
use Lunar\Models\Discount;

class ApplyDiscountCartAction extends Action
{
    public function handle(ActionRequest $request, Cart $models): JsonResponse
    {
        $cart = $models;

        $cart->update([
            'coupon_code' => $request->discountCode,
        ]);

        $cart->refresh()->calculate();

        if (!Discounts::validateCoupon($request->discountCode)) {
            return data([
                'status' => 'invalid_coupon',
                'message' => 'The coupon code is invalid',
            ]);
        }

        $discount = $cart->discounts->first(
            fn (AmountOff $discount) => $discount->discount->coupon === $request->discountCode,
        )?->discount;

        if ($discount && $discount->data['percentage'] === 100 && $this->exceedsMoreThanOneItem($cart)) {
            $cart->update(['coupon_code' => null]);
            $cart->refresh()->calculate();
            return data([
                'status' => 'cannot_apply_coupon',
                'message' => 'You can only apply 100% coupon to cart with one item',
            ]);
        }

        return data([
            'status' => 'valid_coupon',
            'discount' => $discount,
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

    protected function exceedsMoreThanOneItem(Cart $cart): bool
    {
        return $cart->lines->sum('quantity') > 1;
    }
}
