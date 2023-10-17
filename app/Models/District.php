<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;
    public $timestamp = false;

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function village(): HasMany
    {
        return $this->hasMany(Village::class);
    }
}
