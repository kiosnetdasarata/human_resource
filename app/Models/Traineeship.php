<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Division;
use App\Models\InterviewPoint;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Traineeship extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hr_point_id',
        'vacancy_id',
        'slug',
        'nama_lengkap',
        'jk',
        'nomor_telepone',
        'tanggal_lahir',
        'email',
        'alamat',
        'link_sosmed',
        'is_kuliah',
        'nama_instansi',
        'semester',
        'tahun_lulus',
        'role_id',
        'durasi',
        'tanggal_lamaran',
        'status_tahap',
        'file_cv',
        'link_portofolio',
        'sumber_info',
    ];
    protected $appends = ['poin'];

    public function getPoinAttribute()
    {
        if ($this->interviewPoint != null) {
            $poin = $this->interviewPoint;
            $totalPoin = ($poin->presentasi + $poin->kualitas_kerja + $poin->etika
                + $poin->adaptif + $poin->kerja_sama + $poin->disiplin
                + $poin->tanggung_jawab + $poin->inovatif_kreatif
                + $poin->problem_solving + $poin->kemampuan_teknis + $poin->tugas) / 11;

            return $totalPoin;
        }

        return 0;
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'divisi_id');
    }

    public function jobVacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class, 'vacancy_id');
    }

    public function interviewPoint(): BelongsTo
    {
        return $this->belongsTo(InterviewPoint::class, 'hr_point_id');
    }

    protected function getRoleIdAttribute()
    {
        return $this->jobVacancy->role_id;
    }
}
