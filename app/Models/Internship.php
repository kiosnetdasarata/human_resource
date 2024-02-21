<?php

namespace App\Models;

use App\Models\Roles;
use App\Models\InternshipContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Internship extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'internship_nip',
        'nama_lengkap',
        'slug',
        'jk',
        'no_tlpn',
        'email',
        'alamat',
        'link_sosmed',
        'is_kuliah',
        'nama_instansi',
        'semester',
        'tahun_lulus',
        'mitra_id',
        'role_id',
        'durasi',
        'tanggal_masuk',
        'status_internship',
        'status_phase',
        'supervisor',
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

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'supervisor', 'nip');
    }

    public function partnership(): BelongsTo
    {
        return $this->belongsTo(Partnership::class, 'mitra_id');
    }

    public function internshipContract(): HasMany
    {
        return $this->hasMany(InternshipContract::class, 'internship_nip_id', 'internship_nip');
    }
    
}
