<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Village extends Model
{
    use HasFactory;
    
    public $timestamp = false;

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
