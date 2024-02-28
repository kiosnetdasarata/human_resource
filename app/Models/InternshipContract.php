<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternshipContract extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'internship_nip_id',
        'nomor_kontrak',
        'divisi_internship',
        'role_internship',
        'durasi_kontrak',
        'is_expired',
        'date_start',
        'date_expired',
    ];

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class, 'internship_nip_id', 'internship_nip');
    }
}
