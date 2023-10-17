<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Technician extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'team_id',
        'nip_id',
        'katim',
    ];

    // static function boot() 
    // {
    //     parent::boot();

    //     static::creating(function ($sales) {
    //         $sales->uuid = Uuid::uuid4()->getHex();
    //     });
    // }

    public function technicianTeam(): BelongsTo
    {
        return $this->belongsTo(TechnicianTeam::class, 'team_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'nip_id', 'nip');
    }
}
