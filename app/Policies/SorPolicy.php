<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Sor;
use Illuminate\Auth\Access\HandlesAuthorization;

class SorPolicy
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
        return $user->can('view sors');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Sor  $sor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(?User $user, Sor $sor)
    {
        //return $user->can('view sors');
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('create sors');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Sor  $sor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Sor $sor)
    {
        return $user->can('edit sors');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Sor  $sor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Sor $sor)
    {
        return $user->can('delete sors');
    }
}
