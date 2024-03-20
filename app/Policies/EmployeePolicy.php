<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Employee;

class EmployeePolicy
{
    /**
     * Create a new policy instance.
     */

    public function viewAny(User $user)
    {
        return $user->employee->role_id === 1;
    }
    
    public function view(User $user, Employee $employee)
    {
        return $user->nip_id == $employee->nip || $user->employee->role_id === 1;
    }

    public function create(User $user)
    {
        return $user->employee->role_id === 1;
    }

    public function update(User $user, Employee $employee) 
    {
        return $user->nip_id === $employee->nip || $user->employee->role_id === 1;
    }

    public function delete(User $user)
    {
        return $user->employee->role_id === 1;
    }
}
