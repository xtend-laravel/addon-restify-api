<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use App\Notifications\OrderStatusPaymentError;
use App\Notifications\OrderStatusPaymentReceived;
use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Lunar\Base\DataTransferObjects\PaymentAuthorize;
use Lunar\Facades\Payments;
use Lunar\Models\Cart;
use Lunar\PaymentTypes\AbstractPayment;
use Xtend\Extensions\Lunar\Core\Models\Order;
use XtendLunar\Addons\RestifyApi\Notifications\OrderCompletedAdminNotification;
use XtendLunar\Addons\RestifyApi\Notifications\OrderFailedAdminNotification;
use XtendLunar\Addons\RestifyApi\Notifications\RegistrationAdminNotification;
use XtendLunar\Addons\RestifyApi\Notifications\RegistrationCustomerNotification;

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
            /** @var PaymentAuthorize $paymentStatus */
            $paymentStatus = $paymentDriver->init()->authorize();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        $paymentStatus->success
            ? $this->notifyPaymentSuccess($order)
            : $this->notifyPaymentFailure($order);

        return data([
            'paymentStatus' => $paymentStatus,
        ]);
    }

    protected function notifyPaymentSuccess(Order $order): void
    {
        $order->user->notify(new OrderStatusPaymentReceived($order, nl2br(request('notes'))));

        Notification::route('mail', config('mail.from.address'))
            ->notify(new OrderCompletedAdminNotification($order));
    }

    protected function notifyPaymentFailure(Order $order): void
    {
        $order->user->notify(new OrderStatusPaymentError($order));

        Notification::route('mail', config('mail.from.address'))
            ->notify(new OrderFailedAdminNotification($order));
    }
}
