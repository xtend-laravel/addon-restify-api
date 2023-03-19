<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Lunar\Models\Customer;

class CustomerRepository extends Repository
{
    public static string $model = Customer::class;

    public function fields(RestifyRequest $request): array
    {
        return [
            id(),
        ];
    }
}
