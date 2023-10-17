<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilePartnership extends Model
{
    use HasFactory;

    protected $fillable = [
        'mitra_id',
        'file_mou',
        'file_moa',
        'date_start',
        'date_expired',
        'durasi',
    ];

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Partnership::class, 'mitra_id');
    }
}
