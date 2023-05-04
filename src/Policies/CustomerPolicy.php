<?php

namespace XtendLunar\Addons\RestifyApi\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Lunar\Models\Customer;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function allowRestify(User $user = null): bool
    {
        return true;
    }

    public function show(User $user = null, Customer $model): bool
    {
        return true;
    }

    public function store(User $user): bool
    {
        return false;
    }

    public function storeBulk(User $user): bool
    {
        return false;
    }

    public function update(User $user, Customer $model): bool
    {
        return false;
    }

    public function updateBulk(User $user, Customer $model): bool
    {
        return false;
    }

    public function deleteBulk(User $user, Customer $model): bool
    {
        return false;
    }

    public function delete(User $user, Customer $model): bool
    {
        return false;
    }
}
