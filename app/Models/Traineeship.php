<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Division;
use App\Models\InterviewPoint;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Traineeship extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hr_point_id',
        'nama_lengkap',
        'jk',
        'nomor_telepone',
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
        'status_traineeship',
        'file_cv',
        'link_portofolio',
        'sumber_info',
    ];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'divisi_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function interviewPoint(): BelongsTo
    {
        return $this->belongsTo(InterviewPoint::class, 'hr_point_id');
    }
}
