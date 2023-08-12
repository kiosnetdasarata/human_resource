<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeHistory extends Model
{
    use HasFactory;

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'job_titles_id');
    }

    public function jobTitleDivision(): BelongsTo
    {
        return $this->jobTitle()->divisions();
    }


}
