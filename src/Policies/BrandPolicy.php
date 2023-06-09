<?php

namespace XtendLunar\Addons\RestifyApi\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Lunar\Models\Brand;

class BrandPolicy
{
    use HandlesAuthorization;

    public function allowRestify(User $user = null): bool
    {
        return true;
    }

    public function show(User $user = null, Brand $model): bool
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

    public function update(User $user, Brand $model): bool
    {
        return false;
    }

    public function updateBulk(User $user, Brand $model): bool
    {
        return false;
    }

    public function deleteBulk(User $user, Brand $model): bool
    {
        return false;
    }

    public function delete(User $user, Brand $model): bool
    {
        return false;
    }
}
