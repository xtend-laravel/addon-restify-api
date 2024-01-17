<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Lunar\Models\Cart;
use Lunar\Models\Order;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\OrderPresenter;

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

            $order->updateQuietly([
                'customer_id' => $user->customers()->first()->id,
                'user_id' => $user->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        if (app()->environment('production')) {
            $this->sendOrderToZapier($order);
        }

        return data([
            'order' => $order,
        ]);
    }

    protected function sendOrderToZapier(Order $order): void
    {
        $orderData = OrderPresenter::fromData(
            repository: RestifyRepository::resolveWith($order),
            data: $order,
        )->transform(new RestifyRequest(request()->all()));

        Http::post(config('services.zapier.webhook_order_url'), $orderData);
    }
}
