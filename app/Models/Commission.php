<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commission extends Model
{
    use HasFactory;

    public function level() : BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function sales() : HasMany
    {
        return $this->hasMany(Sales::class);
    }
}
