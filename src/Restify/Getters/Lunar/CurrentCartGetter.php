<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\GetterRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Lunar\Models\Cart;
use Lunar\Models\CartLine;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\CartLinePresenter;

class CurrentCartGetter extends Getter
{
    public static $uriKey = 'current-cart';

    public function handle(GetterRequest $request): JsonResponse
    {
        /** @var Cart $cart */
        $cart = $request->has('cartId')
            ? $this->getCartById($request)
            : $this->getCartBySession($request);

        $cart->refresh()->calculate();

        if ($cart->hasCompletedOrders()) {
            $cart = $this->createNewCartFromExistingSession($cart, $request)->refresh()->calculate();
            return data([
                'cart' => $this->cartData($cart, $request),
            ]);
        }

        return data([
            'cart' => $this->cartData($cart, $request),
        ]);
    }

    protected function getCartById(GetterRequest $request): Cart
    {
        /** @var Cart $cart */
        try {
            $cart = Cart::query()->findOrFail($request->cartId);
        } catch (ModelNotFoundException) {
            return $this->getCartBySession($request);
        }

        return $cart;
    }

    protected function getCartBySession(GetterRequest $request): Cart
    {
        /** @var Cart $cart */
        $cart = Cart::query()->firstOrCreate([
            'session_id' => $request->sessionId,
        ], [
            'currency_id' => Currency::getDefault()->id,
            'channel_id' => Channel::getDefault()->id,
            'user_id' => $request->userId ?? null,
        ]);

        return $cart;
    }

    protected function createNewCartFromExistingSession(Cart $cart, GetterRequest $request): Cart
    {
        // $this->ensureDeleteCartsWithSameSessionNoOrders($request);

        $newCart = Cart::query()->create([
            'session_id' => $request->sessionId,
            'currency_id' => Currency::getDefault()->id,
            'channel_id' => Channel::getDefault()->id,
            'user_id' => $request->userId ?? null,
        ]);

        return $newCart;
    }

    protected function ensureDeleteCartsWithSameSessionNoOrders(GetterRequest $request): void
    {
        $carts = Cart::query()->where('session_id', $request->sessionId)->get();
        $carts->each(function (Cart $cart) {
            if ($cart->hasCompletedOrders()) {
                return;
            }

            $cart->lines()->delete();
            $cart->delete();
        });
    }

    protected function cartData(Cart $cart, GetterRequest $request): array
    {
        return [
            'id' => $cart->id,
            'sessionId' => $cart->session_id,
            'lastAddedLineId' => $cart->lines()->latest('updated_at')->first()?->id,
            'lineItems' => $cart->lines->transform(function (CartLine $line) use ($request, $cart) {
                $line->purchasable->load('values.option');

                return CartLinePresenter::fromData(
                    repository: RestifyRepository::resolveWith($cart),
                    data: $line,
                )->transform($request);
            }),
            'totals' => [
                'sub_total' => $cart->subTotal->value,
                'sub_total_discounted' => $cart->subTotalDiscounted->value,
                'discount_total' => $cart->discountTotal?->value,
                'shipping_total' => $cart->shippingTotal?->value,
                'tax_total' => $cart->taxTotal->value,
                'total' => $cart->total->value,
            ],
            'meta' => $cart->meta,
        ];
    }
}
