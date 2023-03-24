<?php

namespace XtendLunar\Addons\RestifyApi\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Lunar\Models\Order;

class OrderPolicy
{
    use HandlesAuthorization;

    public function allowRestify(User $user = null): bool
    {
        return true;
    }

    public function show(User $user = null, Order $model): bool
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

    public function update(User $user, Order $model): bool
    {
        return false;
    }

    public function updateBulk(User $user, Order $model): bool
    {
        return false;
    }

    public function deleteBulk(User $user, Order $model): bool
    {
        return false;
    }

    public function delete(User $user, Order $model): bool
    {
        return false;
    }
}
