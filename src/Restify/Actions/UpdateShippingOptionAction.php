<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Illuminate\Http\Request;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;

class UpdateShippingOptionAction extends Action
{
    public function handle(Request $request, Cart $models): \Illuminate\Http\JsonResponse
    {
        $cart = $models;

        try {
            $shippingOptions = ShippingManifest::getOptions($cart);
            $shippingOption = $shippingOptions->first(fn ($option) => $option->getIdentifier() == $request->identifier);
            if ($shippingOption) {
                $cart->setShippingOption($shippingOption);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

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
