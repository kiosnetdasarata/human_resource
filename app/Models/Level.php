<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_level',
        'nama_level',
        'deskripsi',
    ];

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }
}
