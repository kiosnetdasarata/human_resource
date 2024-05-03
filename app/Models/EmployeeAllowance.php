<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeAllowance extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip_pgwi',
        'allowance_id',
        'tanggal_mulai'
    ];


    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nip_pgwi', 'nip');
    }

    // public function allowance(): BelongsTo
    // {
    //     return $this->belongsTo(AllowanceCategory::class, 'allowance_id');
    // }
}
