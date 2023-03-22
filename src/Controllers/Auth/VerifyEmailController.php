<?php

namespace XtendLunar\Addons\RestifyApi\Controllers\Auth;

use App\Models\User;
use Illuminate\Routing\Controller;

class VerifyEmailController extends Controller
{
    public function __invoke(string $email): void
    {
        User::query()->where('email', $email)->sole();
    }
}
