<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    use HasFactory;
    public $timestamp = false;

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function regencies(): HasMany
    {
        return $this->hasMany(Regency::class);
    }
}
