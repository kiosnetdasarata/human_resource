<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regency extends Model
{
    use HasFactory;
    public $timestamp = false;
    
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }
    
    public function districts(): HasMany
    {
        return $this->HasMany(District::class);
    }

}
