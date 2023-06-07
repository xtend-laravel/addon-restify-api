<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Illuminate\Http\Request;
use Lunar\Models\Cart;
use Lunar\Models\Address;

class UpdateCartAddressAction extends Action
{
    public function handle(Request $request, Cart $models): \Illuminate\Http\JsonResponse
    {
        $cart = $models;
        $address = Address::find($request->id);

        try {
            $cart->addAddress($address, $request->type);
            $cart->{$request->type . '_address_id'} = $address->id;
            $cart->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return ok();
    }
}
