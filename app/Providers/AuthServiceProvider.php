<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        'App\Models\Unit' => 'App\Policies\UnitPolicy',
        'App\Models\UnitGroup' => 'App\Policies\UnitGroupPolicy',
        'App\Models\ResourceGroup' => 'App\Policies\ResourceGroupPolicy',
        'App\Models\TruckSpeed' => 'App\Policies\TruckSpeedPolicy',
        'App\Models\Sor' => 'App\Policies\SorPolicy',
        'App\Models\ResourceCapacityRule' => 'App\Policies\ResourceCapacityRulePolicy',
        'App\Models\PolSkeleton' => 'App\Policies\PolSkeletonPolicy',
        'App\Models\PolRate' => 'App\Policies\PolRatePolicy',
        'App\Models\RateCard' => 'App\Policies\RateCardPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
