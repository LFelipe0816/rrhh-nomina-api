<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'integer',
    ];

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'companies_departments')
            ->using(CompanyDepartment::class)
            ->withPivot('active', 'manager_id')
            ->withTimestamps();
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
