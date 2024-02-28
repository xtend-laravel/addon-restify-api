<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Lunar\Base\Purchasable;
use Lunar\Models\Cart;
use Lunar\Models\CartLine;
use Lunar\Models\ProductVariant;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\CartLinePresenter;

class AddToCartAction extends Action
{
    public function handle(ActionRequest $request, Cart $models): JsonResponse
    {
        $cart = $models;
        $purchasable = $this->getPurchasable($request->product);

        /** @var CartLine | null $purchasableInCart */
        if ($purchasableInCart = $cart->lines()->firstWhere('purchasable_id', $purchasable->id)) {
            $stock = $purchasable->stock - $purchasableInCart->quantity;
            if ($stock < 1) {
                return data([
                    'status' => 'out_of_stock',
                    'message' => 'This product is out of stock',
                ]);
            }
        }

        $cart->add(
            purchasable: $purchasable,
            quantity: $request->product['quantity'] ?? 1,
        )->refresh()->calculate();

        // Log::driver('slack')->debug('AddToCartAction', [
        //     'cart' => $cart->toArray(),
        //     'purchasable' => $purchasable->toArray(),
        //     'request' => $request->toArray(),
        // ]);

        // @todo return any validation stock errors or any other errors when adding lines to cart
        return data($cart->lines->groupBy('purchasable_id')->get($purchasable->id)->flatMap(
            function (CartLine $line) use ($cart, $request) {
                $line->purchasable->load('values.option');
                return CartLinePresenter::fromData(
                    repository: RestifyRepository::resolveWith($cart),
                    data: $line,
                )->transform($request);
            }
        ));
    }

    protected function getPurchasable(array $product): Purchasable|Model
    {
        $variants = array_filter($product['variants'] ?? []);
        // when the product has no variants, we can just return the base variant
        if (blank($variants)) {
            return ProductVariant::query()->where([
                'product_id' => $product['id'],
                'base' => true,
            ])->sole();
        }

        return ProductVariant::query()
            ->where([
                'base' => false,
                'product_id' => $product['id'],
            ])
            ->get()
            ->first(
                fn (ProductVariant $variant) => $variant->values->pluck('id')->diff(array_values($product['variants']))->isEmpty()
            );
    }
}
