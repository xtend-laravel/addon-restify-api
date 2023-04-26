<?php

namespace XtendLunar\Addons\RestifyApi\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Lunar\Models\Address;

class AddressPolicy
{
    use HandlesAuthorization;

    public function allowRestify(User $user = null): bool
    {
        return true;
    }

    public function show(User $user = null, Address $model): bool
    {
        return true;
    }

    public function store(User $user): bool
    {
        return true;
    }

    public function storeBulk(User $user): bool
    {
        return false;
    }

    public function update(User $user, Address $model): bool
    {
        return false;
    }

    public function updateBulk(User $user, Address $model): bool
    {
        return false;
    }

    public function deleteBulk(User $user, Address $model): bool
    {
        return false;
    }

    public function delete(User $user, Address $model): bool
    {
        return false;
    }
}
