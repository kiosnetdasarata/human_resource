<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TechnicianTeam extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';

    public function technician(): HasMany
    {
        return $this->hasMany(Technician::class);
    }
}
