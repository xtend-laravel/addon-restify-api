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
            $cart->setShippingOption($shippingOption);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return ok();
    }
}
