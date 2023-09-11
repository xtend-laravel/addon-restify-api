<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Facades\Payments;
use Lunar\Models\Cart;
use Lunar\Models\Order;
use Lunar\PaymentTypes\AbstractPayment;

class AuthorizePaymentCheckoutAction extends Action
{
    public function handle(ActionRequest $request, Cart $models): JsonResponse
    {
        $cart = $models;
        $order = Order::find($request->orderId);

        if (! $order) {
            return response()->json([
                'message' => __('Order :id not found.', ['id' => $request->orderId]),
            ], 422);
        }

        /** @var AbstractPayment $paymentDriver */
        $paymentDriver = Payments::driver($request->paymentDriver ?? 'stripe');
        $paymentDriver
            ->cart($cart)
            ->order($order)
            ->withData($request->all());

        try {
            $paymentStatus = $paymentDriver->init()->authorize();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return data([
            'paymentStatus' => $paymentStatus,
        ]);
    }
}
