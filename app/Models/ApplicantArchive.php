<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicantArchive extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_vacancies_id',
        'divisi_id',
        'role_id',
        'nama_lengkap',
        'email',
        'no_telepone',
        'alamat',
        'tanggal_lamaran',
        'status_lamaran',
        'file_cv',
        'keterangan',
    ];

}
