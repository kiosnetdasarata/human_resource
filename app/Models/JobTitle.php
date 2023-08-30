<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobTitle extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'divisions_id',
        'nama_jabatan',
        'slug'
    ];

    static function boot()
    {
        parent::boot();

        static::creating(function ($jobTitle) {
            $jobTitle->slug = Str::slug($jobTitle->nama_jabatan, '_');
        });
        
        static::updating(function ($jobTitle) {
            $jobTitle->slug = Str::slug($jobTitle->nama_jabatan, '_');
        });
    }


    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'jabatan_id');
    }

    public function employeesHistory(): HasMany
    {
        return $this->hasMany(EmployeeHistory::class, 'job_titles_id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'divisions_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sales::class, 'jabatan_id');
    }

    public function user(): HasMany
    {
        return $this->hssOne(User::class, 'karyawan_nip');
    }
}
