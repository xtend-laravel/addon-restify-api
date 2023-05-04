<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use XtendLunar\Addons\RestifyApi\Restify\Actions\RemoveLineAction;
use XtendLunar\Addons\RestifyApi\Restify\Actions\UpdateCartAction;
use XtendLunar\Addons\RestifyApi\Restify\Concerns\InteractsWithDefaultFields;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\CheckoutGetter;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\CurrentCartGetter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Lunar\Models\Cart;

class CartRepository extends Repository
{
    use InteractsWithDefaultFields;

    public static string $model = Cart::class;

    public static bool|array $public = true;

    public function actions(RestifyRequest $request): array
    {
        return [
            UpdateCartAction::new()->onlyOnShow(),
            RemoveLineAction::new()->onlyOnShow(),
        ];
    }

    public function getters(RestifyRequest $request): array
    {
        return [
            CurrentCartGetter::new(),
        ];
    }
}
