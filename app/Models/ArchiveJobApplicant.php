<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArchiveJobApplicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacancy_id',
        'nama',
        'no_telp',
        'alamat',
        'link_sosmed',
        'file_cv',
        'keterangan',
        'status',
        'pic',
    ];

    public function jobVacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class, 'vacancy_id');
    }
}
