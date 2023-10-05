<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use App\Models\ApplicantArchive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Division extends Model
{
    use HasFactory;

    protected $primaryKey = 'slug';
    protected $fillable = [
        'kode_divisi',
        'nama_divisi',
        'slug',
        'manager_divisi',
        'no_telp',
        'email',
        'status',
        'deskripsi',
    ];

    static function boot()
    {
        parent::boot();

        static::creating(function ($division) {
            $division->slug = Str::slug($division->nama_divisi, '_');
        });
        
        static::updating(function ($division) {
            $division->slug = Str::slug($division->nama_divisi, '_');
        });
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_divisi', 'nip');
    }
    
    public function internship(): HasMany
    {
        return $this->hasMany(Internship::class, 'divisi_id');
    }//x

    public function jobVacancy(): HasMany
    {
        return $this->hasMany(JobVacancy::class, 'divisi_id');
    }//x

    public function traineeship(): HasMany
    {
        return $this->hasMany(Traineeship::class, 'divisi_id');
    }
}
