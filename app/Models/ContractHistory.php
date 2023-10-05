<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContractHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip_id',
        'file_kontrak',
        'start_kontrak',
        'end_date',
        'verify_hr',
    ];

    public function employees(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nip_id', 'nip');
    }
}
