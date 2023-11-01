<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Technician extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'nip_id',
        'slug',
        'team_id',
        'is_katim',
    ];

    public function technicianTeam(): BelongsTo
    {
        return $this->belongsTo(TechnicianTeam::class, 'team_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nip_id', 'nip');
    }
}
