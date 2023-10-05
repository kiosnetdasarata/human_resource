<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTrainings extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip_id',
        'nama_pelatihan',
        'start_date',
        'end_date',
        'jenis_pelatihan',
        'media_pelatihan',
        'sumber_pelatihan',
        'status_pelatihan',
        'keterangan',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nip_id', 'nip');
    }
}
