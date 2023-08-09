<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Level extends Model
{
    use HasFactory;

    public function commissions(): HasMany
    {
        return $this->hasMany(Commissions::class);
    }
    
    public function sales(): HasMany
    {
        return $this->hasMany(Sales::class, 'jabatan_id');
    }
}
