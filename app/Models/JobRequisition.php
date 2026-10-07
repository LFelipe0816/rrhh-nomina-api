<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobRequisition extends Model
{
    protected $table = 'job_requisitions';

    protected $fillable = [
        'job_id',
        'requesting_manager_id',
        'open_date',
        'expected_close_date',
        'positions_count',
        'status',
    ];

    protected $casts = [
        'job_id' => 'integer',
        'requesting_manager_id' => 'integer',
        'open_date' => 'date',
        'expected_close_date' => 'date',
        'positions_count' => 'integer',
    ];

    public function companyJob(): BelongsTo
    {
        return $this->belongsTo(CompanyJob::class, 'job_id');
    }

    public function requestingManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'requesting_manager_id');
    }
}
