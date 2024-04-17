<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobVacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'min_umur',
        'max_umur',
        'branch_company_id',
        'role_id',
        'slug',
        'open_date',
        'close_date',
        'is_active',
        'is_intern',
        'keterangan'
    ];

    public function countApplicantsByStatus(): Collection
    {
        $applicantCounts = $this->jobapplicant->countBy('status_tahap');

        if ($this->is_intern) {
            $traineeCounts = $this->traineeship->countBy('status_tahap');
            $applicantCounts = $applicantCounts->mergeRecursive($traineeCounts)->map(function ($value, $key) {
                return is_array($value) ? array_sum($value) : $value;
            });
        }

        return $applicantCounts;
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'divisi_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_company_id');
    }

    public function archiveJobApplicant(): HasMany
    {
        return $this->hasMany(JobVacancy::class, 'vacancy_id');
    }

    public function archiveTraineeship(): HasMany
    {
        if ($this->is_intern)
            return $this->hasMany(Traineeship::class, 'vacancy_id');
        else throw new \Exception('vacancy ini bukan untuk traineeship');
    }

    public function jobApplicant(): HasMany
    {
        return $this->hasMany(JobApplicant::class, 'vacancy_id');
    }

    public function traineeship() : ?HasMany
    {
        return $this->hasMany(Traineeship::class, 'vacancy_id');
    }
}

