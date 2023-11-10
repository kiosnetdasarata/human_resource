<?php

namespace App\Models;

use App\Models\User;
use App\Models\Sales;
use App\Models\Presence;
use App\Models\Technician;
use App\Models\ContractHistory;
use App\Models\EmployeeHistory;
use App\Models\EmployeeContract;
use App\Models\EmployeeEducation;
use App\Models\EmployeeTrainings;
use App\Models\EmployeeContractHistory;
use App\Models\EmployeeConfidentalInformation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public function regency(): BelongsTo
    {
        return $this->village()->regency();
    }

    public function district(): BelongsTo
    {
        return $this->regency()->district();
    }

    public function province(): BelongsTo
    {
        return $this->district()->province();
    }
    
    public function contractHistory(): HasMany
    {
        return $this->hasMany(ContractHistory::class, 'nip_id','nip');
    }

    public function employeeCI(): HasOne
    {
        return $this->hasOne(EmployeeConfidentalInformation::class, 'nip_id', 'nip');
    }
    
    public function employeeContract(): HasOne
    {
        return $this->hasOne(EmployeeContract::class, 'nip_id', 'nip');
    }

    public function employeeContractHistory(): HasMany
    {
        return $this->hasMany(EmployeeContractHistory::class, 'nip_id', 'nip');
    }

    public function employeeEducation(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class, 'nip_id', 'nip');
    }

    public function employeeHistory(): HasMany
    {
        return $this->hasMany(EmployeeHistory::class, 'nip_id', 'nip');
    }

    public function employeeTrainings(): HasMany
    {
        return $this->hasMany(EmployeeTrainings::class, 'nip_id', 'nip');
    }

    public function presence(): HasMany
    {
        return $this->hasMany(Presence::class, 'nip','nip');
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

    public function levelStatusAllowance(): HasMany
    {
        return $this->hasMany(LevelStatusAllowance::class, 'nip_id', 'nip');
    }
}
