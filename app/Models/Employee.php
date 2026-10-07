<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'department_id',
        'name',
        'lastname',
        'email',
        'phone',
        'job_position',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'date_of_birth',
        'hire_date',
        'monthly_salary',
        'employment_status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'department_id' => 'integer',
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'monthly_salary' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function managedDepartments(): HasMany
    {
        return $this->hasMany(CompanyDepartment::class, 'manager_id');
    }
}
