<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Cart;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Model;
use Lunar\Facades\ShippingManifest;

class ShippingOptionsGetter extends Getter
{
    public static $uriKey = 'shipping-options';

    public function handle(RestifyRequest $request, ?Model $model = null): \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        $shippingOptions = ShippingManifest::getOptions($model);

        return data($shippingOptions);
    }
}