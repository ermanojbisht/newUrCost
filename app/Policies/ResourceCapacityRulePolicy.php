<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ResourceCapacityRule;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResourceCapacityRulePolicy
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
        return $user->can('view resourcecapacityrules');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceCapacityRule  $resourceCapacityRule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ResourceCapacityRule $resourceCapacityRule)
    {
        return $user->can('view resourcecapacityrules');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('create resourcecapacityrules');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceCapacityRule  $resourceCapacityRule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ResourceCapacityRule $resourceCapacityRule)
    {
        return $user->can('edit resourcecapacityrules');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResourceCapacityRule  $resourceCapacityRule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ResourceCapacityRule $resourceCapacityRule)
    {
        return $user->can('delete resourcecapacityrules');
    }
}
