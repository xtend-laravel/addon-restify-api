<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use XtendLunar\Addons\RestifyApi\Restify\Presenters\CartLinePresenter;
use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\GetterRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use Illuminate\Http\JsonResponse;
use Lunar\Models\CartLine;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\Cart;

class CurrentCartGetter extends Getter
{
    public static $uriKey = 'current-cart';

    public function handle(GetterRequest $request): JsonResponse
    {
        $cart = Cart::query()->firstOrCreate([
            'session_id' => $request->sessionId,
        ], [
            'currency_id' => Currency::getDefault()->id,
            'channel_id' => Channel::getDefault()->id,
            'user_id' => $request->userId ?? null,
        ])->calculate();

        return data([
            'cart' => [
                'id' => $cart->id,
                'products' => $cart->lines->transform(function (CartLine $line) use ($request, $cart) {
                    return CartLinePresenter::fromData(
                        repository: RestifyRepository::resolveWith($cart),
                        data: $line,
                    )->transform($request);
                }),
                'totals' => [
                    'sub_total' => $cart->subTotal->value,
                    'discount_total' => $cart->discountTotal?->value,
                    'shipping_total' => $cart->shippingTotal?->value,
                    'tax_total' => $cart->taxTotal->value,
                    'total' => $cart->total->value,
                ],
                'meta' => $cart->meta,
            ],
        ]);
    }
}
