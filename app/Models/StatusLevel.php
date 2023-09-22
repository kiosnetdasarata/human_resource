<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StatusLevel extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'nama_level',
        'slug'
    ];

    static function boot()
    {
        parent::boot();

        static::creating(function ($statusLevel) {
            $statusLevel->slug = Str::slug($statusLevel->nama_level, '_');
        });
        
        static::updating(function ($statusLevel) {
            $statusLevel->slug = Str::slug($statusLevel->nama_level, '_');
        });
    }

    public function employees() : HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
