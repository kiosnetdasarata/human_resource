<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function internship(): HasMany
    {
        return $this->hasMany(Internship::class, 'mitra_id');
    }

    public function filePartnership(): HasMany
    {
        return $this->hasMany(FilePartnership::class, 'mitra_id')->orderBy('created_at');
    }
}
