<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Cart;

class CreateOrderAction extends Action
{
    public function handle(ActionRequest $request, Cart $models): JsonResponse
    {
        $cart = $models;
        $auth = auth('sanctum');

        /** @var \App\Models\User $user */
        $user = $auth->user();

        if ($auth->check() && $user->customers()->exists()) {
            $request->merge([
                'customer_id' => $user->customers()->first()->id,
                'user_id' => $user->id,
            ]);
        }

        try {
            $order = $cart->createOrder();
            $cart->order_id = $order->id;
            $cart->save();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return data([
            'order' => $order,
        ]);
    }
}
