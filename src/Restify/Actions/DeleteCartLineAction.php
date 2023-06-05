<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Illuminate\Http\Request;
use Lunar\Models\Cart;

class DeleteCartLineAction extends Action
{
    public function handle(Request $request, Cart $models): \Illuminate\Http\JsonResponse
    {
        $cart = $models;

        try {
            $cart->remove($request->input('lineId'))->calculate();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return ok('Cart line item deleted');
    }
}
