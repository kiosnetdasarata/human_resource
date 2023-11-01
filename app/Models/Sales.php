<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales extends Model
{
    use HasFactory, SoftDeletes;
    
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'nip_id',
        'slug',
        'no_tlpn',
        'level_id',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nip_id', 'nip');
    }

    public function levelSales(): BelongsTo
    {
        return $this->belongsTo(LevelSales::class);
    }
}
