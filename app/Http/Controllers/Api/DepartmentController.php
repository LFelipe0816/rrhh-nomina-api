<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Company;
use App\Models\CompanyDepartment;
use App\Models\Department;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Throwable;

class DepartmentController extends Controller
{
    use ApiResponse;

    public function index(Request $request, Company $company)
    {
        try {
            $perPage = (int) $request->query('per_page', 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;

            $departments = $company->departments()
                ->when($request->filled('status'), fn (Builder $query) => $query->where('departments.status', $request->query('status')))
                ->when(
                    $request->filled('search'),
                    fn (Builder $query) => $query->where(function (Builder $builder) use ($request) {
                        $search = '%'.strtolower($request->query('search')).'%';
                        $builder->whereRaw('LOWER(departments.name) LIKE ?', [$search])
                            ->orWhereRaw('LOWER(departments.code) LIKE ?', [$search]);
                    })
                )
                ->select('departments.*')
                ->orderByDesc('departments.id')
                ->paginate($perPage);

            $managerIds = $departments->pluck('pivot.manager_id')->filter()->unique();
            $managers = $managerIds->isNotEmpty()
                ? \App\Models\Employee::whereIn('id', $managerIds)->select('id', 'name', 'lastname')->get()->keyBy('id')
                : collect();

            $departments->getCollection()->transform(function ($dept) use ($managers) {
                $dept->manager_id = $dept->pivot->manager_id;
                $dept->active = $dept->pivot->active;
                $dept->manager = $dept->pivot->manager_id && isset($managers[$dept->pivot->manager_id])
                    ? $managers[$dept->pivot->manager_id]
                    : null;
                return $dept;
            });

            return $this->successResponse($departments);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }
    public function allDepartments(Request $request)
    {
        try {
                $departments = Department::select('departments.*')
                ->orderByDesc('departments.id')->get();

            return $this->successResponse($departments);
        } catch (Exception|Throwable $e) {
            \Log::error('Error fetching all departments: ' . $e->getMessage(), ['exception' => $e]);
            return $this->responseNotFound('An error occurred while fetching departments.');
        }
    }

    public function store(StoreDepartmentRequest $request, Company $company)
    {
        try {
            // Check if department exists by code (Global Lookup)
            $department = Department::where('code', $request->code)->first();

            if (!$department) {
                $department = Department::create([
                    'name' => $request->name,
                    'code' => $request->code,
                    'description' => $request->description,
                    'status' => $request->status ?? 1,
                ]);
            }

            // Attach to company with pivot data
            if (!$company->departments()->where('departments.id', $department->id)->exists()) {
                $company->departments()->attach($department->id, [
                    'active' => true,
                    'manager_id' => $request->input('manager_id'),
                ]);
            } else {
                // Update pivot if needed (e.g. manager change)
                $company->departments()->updateExistingPivot($department->id, [
                    'active' => true,
                    'manager_id' => $request->input('manager_id'),
                ]);
            }

            return $this->successResponse($department, __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    public function show(Company $company, Department $department)
    {
        try {
            $department = $company->departments()->where('departments.id', $department->id)->first();

            if (!$department) {
                return $this->responseNotFound('Department not found in this company');
            }

            $department->manager_id = $department->pivot->manager_id;
            $department->active = $department->pivot->active;
            $department->manager = $department->pivot->manager_id
                ? \App\Models\Employee::select('id', 'name', 'lastname')->find($department->pivot->manager_id)
                : null;

            return $this->successResponse($department);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    public function update(UpdateDepartmentRequest $request, Company $company, Department $department)
    {
        try {
             // Verify association
            if (!$company->departments()->where('departments.id', $department->id)->exists()) {
                 return $this->responseNotFound('Department not found in this company');
            }

            // Update global department details
            $department->update($request->only(['name', 'code', 'description', 'status']));

            // Update pivot
            $pivotData = ['active' => true];
            if ($request->has('manager_id')) {
                $pivotData['manager_id'] = $request->input('manager_id');
            }

            $company->departments()->updateExistingPivot($department->id, $pivotData);

            return $this->successResponse($department->fresh(), __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    public function destroy(Company $company, Department $department)
    {
        try {
            $company->departments()->detach($department->id);
            return $this->successResponse(null, __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }
}
