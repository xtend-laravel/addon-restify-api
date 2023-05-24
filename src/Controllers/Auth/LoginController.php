<?php

namespace XtendLunar\Addons\RestifyApi\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    protected static string $devPassword = 'impersonate';

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required'],
        ]);

        /** * @var User $user */
        if (! $user = config('restify.auth.user_model')::query()
            ->whereEmail($request->input('email'))
            ->first()) {
            abort(401, 'Invalid email.');
        }

        // @todo Need to have some sort of impersonation check here to auto login based on token. (workaround for now to login as any user)
        if (! Hash::check($request->input('password'), $user->password) && $request->input('password') !== static::$devPassword) {
            abort(401, 'Invalid password.');
        }

        return data([
            'user' => $user,
            'token' => $user->createToken('login'),
        ]);
    }
}
