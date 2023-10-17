<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LevelSales extends Model
{
    use HasFactory;

    public function commissions(): HasMany
    {
        return $this->hasMany(Commissions::class);
    }//x
    
    public function sales(): HasMany
    {
        return $this->hasMany(Sales::class);
    }//x
}
