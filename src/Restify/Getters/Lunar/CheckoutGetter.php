<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\GetterRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Address;
use Xtend\Extensions\Lunar\Core\Models\Cart;

class CheckoutGetter extends Getter
{
    public static $uriKey = 'checkout';

    public function handle(GetterRequest $request): JsonResponse
    {
        // @todo fetch cart from session
        /** @var \Lunar\Models\Cart $cart */
        try {
            $cart = Cart::findOrFail($request->cartId);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        $shippingOptions = ShippingManifest::getOptions($cart);

        return data([
            'selectedAddressIds' => [
                'shipping' => $cart->shipping_address_id,
                'billing' => $cart->billing_address_id,
            ],
            'selectedShippingIdentifier' => $cart->getShippingOption()?->getIdentifier(),
            'addresses' => $this->getCustomerAddresses($request),
            'shipping_methods' => $shippingOptions,
        ]);
    }

    protected function getCustomerAddresses(GetterRequest $request): array
    {
        /** @var \Lunar\Models\Customer $customer */
        $customer = $request->user()?->customers()?->first();

        return $customer ? $customer->addresses->toArray() : [];
    }
}
