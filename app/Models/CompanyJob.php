<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyJob extends Model
{
    protected $table = 'company_jobs';

    protected $fillable = [
        'company_id',
        'name',
        'job_id',
        'department_id',
        'boss_id',
        'active',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'job_id' => 'integer',
        'department_id' => 'integer',
        'boss_id' => 'integer',
        'active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function jobLevel(): BelongsTo
    {
        return $this->belongsTo(JobLevel::class, 'job_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function boss(): BelongsTo
    {
        return $this->belongsTo(CompanyJob::class, 'boss_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(CompanyJob::class, 'boss_id');
    }

    public function requisitions(): HasMany
    {
        return $this->hasMany(JobRequisition::class, 'job_id');
    }
}
