<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'nama_divisi',
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

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'divisi_id');
    }

    public function jobTitle(): HasMany
    {
        return $this->hasMany(JobTitle::class, 'divisions_id');
    }
}
