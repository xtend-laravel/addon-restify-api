<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Illuminate\Http\Request;
use Lunar\Models\Cart;

class RemoveLineAction extends Action
{
    public function handle(Request $request, Cart $models): \Illuminate\Http\JsonResponse
    {
        $cart = $models;

        $cart->remove($request->input('lineId'))->calculate();

        return ok();
    }
}
