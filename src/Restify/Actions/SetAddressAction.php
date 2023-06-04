<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Illuminate\Http\Request;
use Lunar\Models\Cart;
use Lunar\Models\Address;

class SetAddressAction extends Action
{
    public function handle(Request $request, Cart $cart): \Illuminate\Http\JsonResponse
    {
        $address = Address::find($request->id);

        $cart->addAddress($address, $request->type);

        return ok();
    }
}
