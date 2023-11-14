<?php

namespace App\Models;

use App\Models\Level;
use App\Models\Division;
use App\Models\Internship;
use App\Models\JobVacancy;
use App\Models\Traineeship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'divisi_id',
        'kode_jabatan',
        'nama_jabatan',
        'level_id',
        'deskripsi',
    ];
    
    public function division():BelongsTo
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
