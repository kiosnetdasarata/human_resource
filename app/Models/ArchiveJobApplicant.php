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
        'hr_point_id',
        'nama_lengkap',
        'slug',
        'jk',
        'no_tlpn',
        'email',
        'alamat',
        'role_id',
        'tanggal_lamaran',
        'status_lamaran',
        'link_sosmed',
        'file_cv',
        'keterangan',
        'is_intern',        
    ];
    protected $guard = ['id'];

    public function jobApplicant()
    {
        return $this->where('is_intern', 0);
    }

    public function traineeship()
    {
        return $this->where('is_intern', 0);
    }
}
