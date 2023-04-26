<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use Lunar\Models\Address;
use XtendLunar\Addons\RestifyApi\Restify\Concerns\InteractsWithDefaultFields;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\AddressPresenter;

class AddressRepository extends Repository
{
    use InteractsWithDefaultFields;

    public static string $model = Address::class;

    public static string $presenter = AddressPresenter::class;
}
