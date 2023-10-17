<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeContractHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip_id',
        'nomor_kontrak',
        'jenis_kontrak',
        'file_terms',
        'start_kontrak',
        'end_kontrak',
        'kontrak_ke'
    ];

    public function employee(): BelongsTo 
    {
        return $this->belongsTo(Employee::class, 'nip_id', 'nip');
    }
}
