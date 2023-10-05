<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Traineeship extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lengkap',
        'divisi_id',
        'role_id',
        'durasi',
        // 'slug',
        'email',
        'nomor_telepone',
        'alamat',
        'tanggal_lamaran',
        'status_traineeship',
        'file_cv',
    ];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'divisi_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
    
    public function internship(): HasOne
    {
        return $this->hasOne(Internship::class);
    }//x
}
