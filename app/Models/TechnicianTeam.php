<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TechnicianTeam extends Model
{
    use HasFactory;

    protected $connection = 'mysql4';

    public function technicians(): HasMany
    {
        return $this->hasMany(Technician::class, 'team_id');
    }
}