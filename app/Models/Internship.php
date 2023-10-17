<?php

namespace App\Models;

use App\Models\Roles;
use App\Models\InternshipContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Internship extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'traineeship_id',
        'internship_nip',
        'nama_lengkap',
        'slug',
        'alamat',
        'email',
        'no_telp',
        'divisi_id',
        'role_id',
        'supervisor',
        'tanggal_masuk',
        'durasi',
        'file_cv',
        'is_mitra',
        'mitra_id',
    ];

    public function traineeship(): BelongsTo
    {
        return $this->belongsTo(Traineeship::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'divisi_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Roles::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'supervisor', 'nip');
    }

    public function partnership(): BelongsTo
    {
        return $this->belongsTo(Partnership::class, 'mitra_id');
    }

    public function internshipContract(): HasOne
    {
        return $this->hasOne(InternshipContract::class, 'internship_nip_id');
    }//x
    
}
