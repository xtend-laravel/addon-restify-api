<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Lunar\Models\Cart;
use XtendLunar\Addons\RestifyApi\Restify\Actions\AuthorizePaymentCheckoutAction;
use XtendLunar\Addons\RestifyApi\Restify\Actions\CreateOrderAction;
use XtendLunar\Addons\RestifyApi\Restify\Actions\DeleteCartLineAction;
use XtendLunar\Addons\RestifyApi\Restify\Actions\UpdateCartAddressAction;
use XtendLunar\Addons\RestifyApi\Restify\Actions\AddToCartAction;
use XtendLunar\Addons\RestifyApi\Restify\Actions\UpdateCartLineAction;
use XtendLunar\Addons\RestifyApi\Restify\Actions\UpdateShippingOptionAction;
use XtendLunar\Addons\RestifyApi\Restify\Concerns\InteractsWithDefaultFields;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Cart\ShippingOptionsGetter;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\CurrentCartGetter;

class CartRepository extends Repository
{
    use InteractsWithDefaultFields;

    public static string $model = Cart::class;

    public static bool|array $public = true;

    public function actions(RestifyRequest $request): array
    {
        return [
            AddToCartAction::new()->onlyOnShow(),
            DeleteCartLineAction::new()->onlyOnShow(),
            UpdateCartLineAction::new()->onlyOnShow(),
            UpdateCartAddressAction::new()->onlyOnShow(),
            UpdateShippingOptionAction::new()->onlyOnShow(),
            AuthorizePaymentCheckoutAction::new()->onlyOnShow(),
            CreateOrderAction::new()->onlyOnShow(),
        ];
    }

    public function getters(RestifyRequest $request): array
    {
        return [
            CurrentCartGetter::new(),
            ShippingOptionsGetter::new(),
        ];
    }
}
