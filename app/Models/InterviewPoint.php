<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InterviewPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'presentasi',
        'kualitas_kerja',
        'etika',
        'adaptif',
        'kerja_sama',
        'disiplin',
        'tanggung_jawab',
        'inovatif_kreatif',
        'problem_solving',
        'kemampuan_teknis',
        'tugas',
        'keterangan_hr',
        'keterangan_user',
    ];

    public function traineeship(): HasOne
    {
        return $this->hasOne(Traineeship::class, 'hr_point_id');
    }

}
