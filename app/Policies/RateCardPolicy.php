<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RateCard;
use Illuminate\Auth\Access\HandlesAuthorization;

class RateCardPolicy
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
        return $user->can('view ratecards');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RateCard  $rateCard
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, RateCard $rateCard)
    {
        return $user->can('view ratecards');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('create ratecards');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RateCard  $rateCard
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, RateCard $rateCard)
    {
        return $user->can('edit ratecards');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RateCard  $rateCard
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, RateCard $rateCard)
    {
        return $user->can('delete ratecards');
    }
}
