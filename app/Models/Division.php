<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Employee;
use App\Models\Internship;
use App\Models\JobVacancy;
use App\Models\Traineeship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Division extends Model
{
    use HasFactory;
    protected $table = 'divisions';

    protected $fillable = [
        'nama_divisi',
        'slug',
        'kode_divisi',
        'manager_divisi',
        'email',
        'no_tlpn',
        'status',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_divisi', 'nip');
    }
    
    public function internship(): HasMany
    {
        return $this->hasMany(Internship::class, 'divisi_id');
    }//x

    public function jobVacancy(): HasMany
    {
        return $this->hasMany(JobVacancy::class, 'divisi_id');
    }//x

    public function traineeship(): HasMany
    {
        return $this->hasMany(Traineeship::class, 'divisi_id');
    }

    public function role(): HasMany
    {
        return $this->hasMany(Role::class, 'divisi_id');
    }
}
