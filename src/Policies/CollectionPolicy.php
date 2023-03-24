<?php

namespace XtendLunar\Addons\RestifyApi\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Lunar\Models\Collection;

class CollectionPolicy
{
    use HandlesAuthorization;

    public function allowRestify(User $user = null): bool
    {
        return true;
    }

    public function show(User $user = null, Collection $model): bool
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

    public function update(User $user, Collection $model): bool
    {
        return false;
    }

    public function updateBulk(User $user, Collection $model): bool
    {
        return false;
    }

    public function deleteBulk(User $user, Collection $model): bool
    {
        return false;
    }

    public function delete(User $user, Collection $model): bool
    {
        return false;
    }
}
