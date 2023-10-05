<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partnership extends Model
{
    use HasFactory;

    public function internship(): HasMany
    {
        return $this->hasMany(Internship::class, 'mitra_id');
    }//x
}
