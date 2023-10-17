<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEducation extends Model
{
    use HasFactory;

    protected $table = 'employee_educations';
    protected $fillable = [
        'nip_id',
        'pendidikan_terakhir',
        'nama_instansi',
        'tahun_lulus',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nip_id', 'nip');
    }
}
