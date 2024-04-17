<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Partnership extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_mitra',
        'alamat',
        'perwakilan_mitra',
        'no_tlpn',
        'kategori_mitra',
        'email',
    ];

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class, 'mitra_id');
    }

    public function filesHistory(): HasMany
    {
        return $this->hasMany(FilePartnership::class, 'mitra_id')->orderBy('created_at');
    }

    public function file(): HasOne
    {
        return $this->files()->one()->latestOfMany();
    }
}
