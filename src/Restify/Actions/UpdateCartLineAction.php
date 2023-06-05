<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Illuminate\Http\Request;
use Lunar\Models\Cart;

class UpdateCartLineAction extends Action
{
    public function handle(Request $request, Cart $models): \Illuminate\Http\JsonResponse
    {
        $cart = $models;

        $cart->updateLine(
            cartLineId: $request->lineId,
            quantity: $request->quantity,
        );

        return ok();
    }
}
