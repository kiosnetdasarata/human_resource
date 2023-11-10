<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplicant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vacancy_id',
        'hr_point_id',
        'nama_lengkap',
        'tanggal_lahir',
        'jk',
        'alamat',
        'email',
        'no_tlpn',
        'pendidikan_terakhir',
        'nama_instansi',
        'tahun_lulus',
        'link_sosmed',
        'role_id',
        'pengalaman',
        'ekspetasi_gaji',
        'date',
        'file_cv',
        'link_portofolio',
        'sumber_info',
        'status_tahap',
    ];

    public function jobVacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class, 'vacancy_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
