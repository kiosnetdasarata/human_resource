<?php

namespace App\Models;

use App\Models\User;
use App\Models\Sales;
use App\Models\Technician;
use App\Models\EmployeeContract;
use App\Models\EmployeeEducation;
use App\Models\EmployeeContractHistory;
use App\Models\EmployeeConfidentalInformation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employee_personal_informations';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'branch_company_id',
        'nip',
        'role_id',
        'level_id',
        'nama',
        'slug',
        'alamat',
        'alamat_sekarang',
        'dusun_id',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'no_tlpn',
        'email',
        'agama',
        'status_perkawinan',
        'foto_profil',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_company_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'dusun_id');
    }

    public function district()
    {
        return $this->village->district();
    }

    public function regency(): BelongsTo
    {
        return $this->district->regency();
    }

    public function province(): BelongsTo
    {
        return $this->regency->province();
    }

    public function employeeCI(): HasOne
    {
        return $this->hasOne(EmployeeConfidentalInformation::class, 'nip_id', 'nip');
    }

    public function contract(): HasOne
    {
        return $this->hasOne(EmployeeContract::class, 'nip_id', 'nip');
    }

    public function contractsHistory(): HasMany
    {
        return $this->hasMany(EmployeeContractHistory::class, 'nip_id', 'nip');
    }

    public function educationHistory(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class, 'nip_id', 'nip')->orderByDesc('created_at');
    }

    public function education(): HasOne
    {
        return $this->educationHistory()->one()->latestOfMany();
    }

    public function sales(): HasOne
    {
        return $this->hasOne(Sales::class, 'nip_id', 'nip');
    }

    public function technician(): HasOne
    {
        return $this->hasOne(Technician::class, 'nip_id', 'nip');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'nip_id', 'nip');
    }

    public function leaderOf(): HasOne
    {
        return $this->hasOne(Division::class, 'manajer_divisi', 'nip');
    }

    public function division(): HasOneThrough
    {
        return $this->hasOneThrough(Division::class, Role::class, 'id', 'id', 'role_id', 'divisi_id');
    }

    public function levelStatusAllowance(): HasMany
    {
        return $this->hasMany(LevelStatusAllowance::class, 'nip_id', 'nip');
    }
}
