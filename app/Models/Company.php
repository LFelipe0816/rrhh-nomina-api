<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'legal_name',
        'rnc',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'country_code',
        'status',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'integer',
    ];

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'companies_departments')
            ->using(CompanyDepartment::class)
            ->withPivot('active', 'manager_id')
            ->withTimestamps();
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function payrollPeriods(): HasMany
    {
        return $this->hasMany(PayrollPeriod::class);
    }

    public function companyDepartments(): HasMany
    {
        return $this->hasMany(CompanyDepartment::class);
    }
}
