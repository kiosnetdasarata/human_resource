<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql3';
    protected $table = 'branch_companies';

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'branch_company_id');
    }

    public function levels(): HasMany
    {
        return $this->hasMany(Level::class, 'branch_company_id');
    }
}
