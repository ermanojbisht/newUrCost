<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PolSkeleton;
use Illuminate\Auth\Access\HandlesAuthorization;

class PolSkeletonPolicy
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
        return $user->can('view polskeletons');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PolSkeleton  $polSkeleton
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, PolSkeleton $polSkeleton)
    {
        return $user->can('view polskeletons');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('create polskeletons');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PolSkeleton  $polSkeleton
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, PolSkeleton $polSkeleton)
    {
        return $user->can('edit polskeletons');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PolSkeleton  $polSkeleton
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, PolSkeleton $polSkeleton)
    {
        return $user->can('delete polskeletons');
    }
}
