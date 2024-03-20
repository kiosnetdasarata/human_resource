<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Employee;

// use Illuminate\Support\Facades\Gate;
use App\Policies\EmployeePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Employee::class, EmployeePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('is-hr', function (User $user) {
            return $user->employee->role_id === 3;
        });
    }
}
