<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class EmployeeConfidentalInformation extends Model
{
    use HasFactory;
    
    protected $table = 'employee_confidential_informations';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'nip_id',
        'nik',
        'nomor_bpjs',
        'nama_bank',
        'nomor_rekening',
        'no_tlpn_darurat',
        'nama_kontak_darurat',
        'status_kontak_darurat',      
        'foto_ktp',
        'foto_kk',
        'file_cv',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nip_id', 'nip');
    }

    public function employeeContract(): HasOne
    {
        return $this->employee()->employeeContract();
    }
}
