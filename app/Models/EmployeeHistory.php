<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip_id',
        'from_divisi',
        'to_divisi',
        'from_position',
        'to_position',
        'from_position_level',
        'to_position_level',
        'date_history',
        'deskripsi',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nip', 'nip');
    }

    public function fromDivision(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'from_division');
    }

    public function toDivision(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'to_division');
    }

    public function fromJobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'from_position');
    }

    public function toJobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'to_position');
    }

    public function toPositionLevel(): BelongsTo
    {
        return $this->belongsTo(PositionLevel::class, 'to_position_level');
    }

    public function from_position_level(): BelongsTo
    {
        return $this->belongsTo(PositionLevel::class, 'from_position_level');
    }
}
