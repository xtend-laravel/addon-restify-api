<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use Binaryk\LaravelRestify\Fields\HasOne;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Lunar\Models\Order;
use XtendLunar\Addons\RestifyApi\Restify\Concerns\InteractsWithDefaultFields;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\CheckoutGetter;

class OrderRepository extends Repository
{
    use InteractsWithDefaultFields;

    public static string $model = Order::class;

    public static function related(): array
    {
        return [
            HasOne::make('user', UserRepository::class),
        ];
    }

    public function getters(RestifyRequest $request): array
    {
        return [
            CheckoutGetter::new(),
        ];
    }
}
