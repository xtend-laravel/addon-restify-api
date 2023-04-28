<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use App\Models\User;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\UserPresenter;

class UserRepository extends Repository
{
    public static string $model = User::class;

    public static string $presenter = UserPresenter::class;
}
