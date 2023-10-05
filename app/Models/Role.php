<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'divisi_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function internship(): HasMany
    {
        return $this->hasMany(Internship::class);
    }//x

    public function jobVacancy(): HasMany
    {
        return $this->hasMany(JobVacancy::class);
    }//x

    public function traineeship(): HasMany
    {
        return $this->hasMany(Traineeship::class);
    }
}
