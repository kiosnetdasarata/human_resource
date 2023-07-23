<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Technician extends Model
{
    use HasFactory;

    public function technicianTeam(): BelongsTo
    {
        return $this->belongsTo(TechnicianTeam::class, 'team_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }
}
