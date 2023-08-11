<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;
    protected $primaryKey = 'nip_pgwi';
    protected $guard = [
        'nip_pgwi'
    ];
    protected $fillable = [
        'brach_company_id',
        'divisi_id',
        'jabatan_id',
        'no_tlpn',
        'email',
        'nik',
        'nama',
        'jk',
        'province_id',
        'regencie_id',
        'district_id',
        'village_id',
        'almt_detail',
        'tgl_lahir',
        'agama',
        'status_perkawinan',
        'tempat_lahir',
        'nama_instansi',
        'tahun_lulus'
    ];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'divisi_id');
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'jabatan_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'regencie_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function sales(): HasOne
    {
        return $this->hasOne(Sales::class);
    }

    public function technician(): HasOne
    {
        return $this->hasOne(Technician::class);
    }

    public function user(): HasOne
    {
        return $this->hssOne(User::class, 'karyawan_nip');
    }
}