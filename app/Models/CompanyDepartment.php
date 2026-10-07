<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyDepartment extends Pivot
{
    protected $table = 'companies_departments';

    public $incrementing = true;

    public $timestamps = true;

    /**
     * Mass-assignable attributes.
     */
    protected $fillable = [
        'company_id',
        'department_id',
        'manager_id',
        'parent_company_department_id',
        'active',
    ];

    /**
     * Type casting for attributes.
     */
    protected $casts = [
        'id'                          => 'integer',
        'company_id'                  => 'integer',
        'department_id'               => 'integer',
        'manager_id'                  => 'integer',
        'parent_company_department_id' => 'integer',
        'active'                      => 'boolean',
    ];

    /**
     * Relationships
     */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Departamento padre en esta empresa
     */
    public function parent()
    {
        return $this->belongsTo(CompanyDepartment::class, 'parent_company_department_id');
    }

    /**
     * Subdepartamentos directos en esta empresa
     */
    public function children()
    {
        return $this->hasMany(CompanyDepartment::class, 'parent_company_department_id');
    }

    /**
     * Subdepartamentos recursivos (todos los niveles)
     */
    public function descendants()
    {
        return $this->hasMany(CompanyDepartment::class, 'parent_company_department_id')
            ->with(['descendants', 'department:id,name,code']);
    }

    /**
     * Ancestros recursivos
     */
    public function ancestors()
    {
        return $this->belongsTo(CompanyDepartment::class, 'parent_company_department_id')
            ->with(['ancestors', 'department:id,name,code']);
    }

    /**
     * Verificar si es departamento raíz en esta empresa
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_company_department_id);
    }

    /**
     * Verificar si tiene subdepartamentos
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Scopes
     */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForDepartment(Builder $query, int $departmentId): Builder
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_company_department_id');
    }
}
