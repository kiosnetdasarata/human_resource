<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql3';
    protected $table = 'brach_company';

    public function employees():HasMany
    {
        return $this->hasMany(Employee::class, 'brach_company_id');
    }
}
