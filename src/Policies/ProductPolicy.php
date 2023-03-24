<?php

namespace XtendLunar\Addons\RestifyApi\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Lunar\Models\Product;

class ProductPolicy
{
    use HandlesAuthorization;

    public function allowRestify(User $user = null): bool
    {
        return true;
    }

    public function show(User $user = null, Product $model): bool
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

    public function update(User $user, Product $model): bool
    {
        return false;
    }

    public function updateBulk(User $user, Product $model): bool
    {
        return false;
    }

    public function deleteBulk(User $user, Product $model): bool
    {
        return false;
    }

    public function delete(User $user, Product $model): bool
    {
        return false;
    }
}
