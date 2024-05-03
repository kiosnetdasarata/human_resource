<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AllowanceCategory extends Model
{
    use HasFactory;

    protected $fillable= [
        'slug',
        'nama_kategori',
        'kategori',
        'keterangan',
        'syarat_khusus',
        'min_level',
        'max_level',
    ];

    public function employee(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_allowances', 'allowance_id', 'nip_pgwi', 'id', 'nip')
                    ->withPivot('tanggal_mulai');
    }

    public function level(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'level_status_allowances', 'allowance_id', 'nip_id', 'id', 'nip')
                    ->withPivot('keterangan');
    }
}
