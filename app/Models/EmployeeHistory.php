<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeHistory extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    protected $incrementing = false;
    protected $fillable = [
        'uuid',
        'pgwi_nip',
        'divisions_id',
        'job_titles_id',
        'tgl_berakhir',
        'after_divisi_id',
        'after_job_title_id',
        'keterangan',
    ];

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'job_titles_id');
    }

    public function jobTitleDivision(): BelongsTo
    {
        return $this->jobTitle()->divisions();
    }


}
