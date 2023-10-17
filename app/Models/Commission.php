<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Commission extends Model
{
    use HasFactory;

    public function levelSales(): BelongsTo
    {
        return $this->belongsTo(LevelSales::class, 'level_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(LevelSales::class, 'sales');
    }
}
