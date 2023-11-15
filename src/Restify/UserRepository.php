<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use App\Models\User;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Support\Facades\Hash;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\AuthenticatedUserGetter;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\UserPresenter;

class UserRepository extends Repository
{
    public static string $model = User::class;

    public static string $presenter = UserPresenter::class;

    public function fields(RestifyRequest $request): array
    {
        return [
            field('id'),
            field('first_name')->rules('required'),
            field('last_name')->rules('required'),
            field('title'),
            field('company_name'),
            field('meta'),
            field('email'),
        ];
    }

    public function update(RestifyRequest $request, $userId)
    {
        $user = User::findOrFail($userId);

        $user->update([
            'email' => $request->input('email'),
        ]);

        if ($request->has('new_password')) {
            $user->update([
                'password' => Hash::make($request->input('new_password')),
            ]);
        }

        $customer = $user->customers()->first();

        $customer->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'meta' => $request->input('meta'),
            'company_name' => $request->input('company_name'),
        ]);

        return data($customer);
    }

    public function getters(RestifyRequest $request): array
    {
        return [
            AuthenticatedUserGetter::new(),
        ];
    }
}
