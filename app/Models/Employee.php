<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use App\Models\Branch;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guard = [
        
    ];

    protected $fillable = [
        'uuid',
        'nip_pgwi',
        'slug',
        'branch_company_id',
        'divisi_id',
        'jabatan_id',
        'status_level_id',
        'tgl_mulai_kerja',
        'no_tlpn',
        'email',
        'nik',
        'nama',
        'nickname',
        'agama',
        'jk',
        'tgl_lahir',
        'tempat_lahir',
        'almt_detail',
        'province_id',
        'regencie_id',
        'district_id',
        'village_id',
        'status_perkawinan',
        'nama_instansi',
        'tahun_lulus',
        'pendidikan_terakhir',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_company_id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'divisi_id');
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'jabatan_id');
    }

    public function statusLevel(): BelongsTo
    {
        return $this->belongsTo(StatusLevel::class);
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
        return $this->hasOne(Sales::class, 'karyawan_nip');
    }

    public function technician(): HasOne
    {
        return $this->hasOne(Technician::class, 'employees_nip');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'karyawan_nip');
    }


}
