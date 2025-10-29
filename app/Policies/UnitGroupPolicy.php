<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UnitGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnitGroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->can('view unitgroups');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UnitGroup  $unitGroup
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, UnitGroup $unitGroup)
    {
        return $user->can('view unitgroups');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('create unitgroups');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UnitGroup  $unitGroup
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, UnitGroup $unitGroup)
    {
        return $user->can('edit unitgroups');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UnitGroup  $unitGroup
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, UnitGroup $unitGroup)
    {
        return $user->can('delete unitgroups');
    }
}
