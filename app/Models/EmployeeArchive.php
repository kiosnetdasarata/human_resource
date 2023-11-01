<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeArchive extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip_id',
        'nik',
        'nama',
        'alamat',
        'dusun_id',
        'tgl_lahir',
        'email',
        'no_tlpn',
        'tgl_masuk',
        'divisi_id',
        'role_id',
        'level_id',
        'nomor_bpjs',
        'rekening_bank',
        'nama_bank',
        'nama_kontak_darurat',
        'no_tlpn_darurat',
        'status_kontak_darurat',
        'nomor_kontrak',
        'status_terminate',
        'tanggal_terminate',
        'file_kontrak',
        'foto_ktp',
        'foto_kk',
        'file_cv',
    ];
}
