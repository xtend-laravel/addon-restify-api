<?php

namespace XtendLunar\Addons\RestifyApi\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Lunar\Models\Customer;

class RegisterController extends Controller
{
    protected Customer $customer;

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:' . Config::get('restify.auth.table', 'users')],
            'title' => ['sometimes', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['string', 'max:255'],
            'password' => ['sometimes', 'required', 'confirmed'],
        ]);

        $user = $this
            ->createCustomer($request)
            ->attachUser($request);

        return data([
            'user' => $user,
            'token' => $user->createToken('login')
        ]);
    }

    protected function createCustomer(Request $request): self
    {
        $this->customer = Customer::query()->create([
            'title' => $request->input('title'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
        ]);

        return $this;
    }

    protected function attachUser(Request $request): User
    {
        return $this->customer->users()->create([
            'name' => $this->customer->fullName,
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password', 'secret20')),
        ]);
    }
}
