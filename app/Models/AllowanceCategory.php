<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AllowanceCategory extends Model
{
    use HasFactory;
    
    public function levelStatusAllowance(): HasMany
    {
        return $this->hasMany(LevelStatusAllowance::class, 'allowance_id');
    }
}
