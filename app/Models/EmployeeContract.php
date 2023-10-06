<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nip_id',
        'nomor_kontrak',
        'jenis_kontrak',
        'start_kontrak',
        'end_kontrak',
        'work_start',
        'supervisor',
        'file_terms',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nip_id', 'nip');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nip_id', 'nip');
    }

    public function employeeCI(): HasOne
    {
        return $this->employee()->employeeConfidentalInformation();
    }
}
