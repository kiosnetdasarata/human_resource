<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use HasFactory;

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'divisi_id');
    }

    public function jobTitle(): HasMany
    {
        return $this->hasMany(JobTitle::class, 'divisions_id');
    }
}
