<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacancy_id',
        'nama_lengkap',
        'role_dilamar',
        'email',
        'no_telp',
        'alamat',
        'link_sosmed',
        'file_cv',
        'status_tahap',
    ];

    public function jobVacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class, 'vacancy_id');
    }
}
