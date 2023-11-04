<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\GetterRequest;
use Illuminate\Http\JsonResponse;

class AuthenticatedUserGetter extends Getter
{
    public static $uriKey = 'authenticated-user';

    public function handle(GetterRequest $request): JsonResponse
    {
        $user = $request->user();
        $cart = $user->cart;

        return data([
            'user' => $user,
            'cartId' => $cart?->id,
            'token' => $user->createToken('login'),
        ]);
    }
}
