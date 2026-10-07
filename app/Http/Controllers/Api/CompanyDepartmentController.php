<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyDepartmentRequest;
use App\Http\Requests\UpdateCompanyDepartmentRequest;
use App\Models\Company;
use App\Models\CompanyDepartment;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CompanyDepartmentController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Company $company): JsonResponse
    {
        try {
            $perPage = (int) $request->query('per_page', 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;

            $query = CompanyDepartment::forCompany($company->id)
                ->with(['department:id,name,code', 'manager:id,name,lastname', 'parent.department:id,name,code']);

            // Filtrar solo departamentos raíz
            if ($request->boolean('roots_only')) {
                $query->roots();
            }

            // Filtrar por padre específico
            if ($request->filled('parent_id')) {
                $query->where('parent_company_department_id', $request->query('parent_id'));
            }

            $companyDepartments = $query->paginate($perPage);

            return $this->successResponse($companyDepartments);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Get all resources without pagination.
     */
    public function all(Request $request, Company $company): JsonResponse
    {
        try {
            $query = CompanyDepartment::forCompany($company->id)
                ->with(['department:id,name,code', 'manager:id,name,lastname', 'parent.department:id,name,code']);

            if ($request->boolean('roots_only')) {
                $query->roots();
            }

            if ($request->filled('parent_id')) {
                $query->where('parent_company_department_id', $request->query('parent_id'));
            }

            return $this->successResponse($query->get());
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyDepartmentRequest $request, Company $company): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['company_id'] = $company->id;

            $companyDepartment = CompanyDepartment::create($data);

            return $this->successResponse(
                $companyDepartment->load(['department:id,name,code', 'manager:id,name,lastname', 'parent.department:id,name,code']),
                __('responses.success_operation'),
                201
            );
        } catch (Exception|Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company, CompanyDepartment $companyDepartment): JsonResponse
    {
        try {
            if ($companyDepartment->company_id !== $company->id) {
                return $this->responseNotFound(__('responses.resource_not_found'));
            }

            return $this->successResponse(
                $companyDepartment->load(['department:id,name,code', 'manager:id,name,lastname', 'parent.department:id,name,code', 'children.department:id,name,code'])
            );
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyDepartmentRequest $request, Company $company, CompanyDepartment $companyDepartment): JsonResponse
    {
        try {
            if ($companyDepartment->company_id !== $company->id) {
                return $this->responseNotFound(__('responses.resource_not_found'));
            }

            $companyDepartment->update($request->validated());

            return $this->successResponse(
                $companyDepartment->fresh()->load(['department:id,name,code', 'manager:id,name,lastname', 'parent.department:id,name,code']),
                __('responses.success_operation')
            );
        } catch (Exception|Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Obtener árbol jerárquico de departamentos de la empresa
     */
    public function tree(Company $company): JsonResponse
    {
        try {
            $tree = CompanyDepartment::forCompany($company->id)
                ->roots()
                ->with(['department:id,name,code', 'manager:id,name,lastname', 'descendants'])
                ->get();

            return $this->successResponse($tree);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Obtener subdepartamentos de un departamento específico
     */
    public function children(Company $company, CompanyDepartment $companyDepartment): JsonResponse
    {
        try {
            if ($companyDepartment->company_id !== $company->id) {
                return $this->responseNotFound(__('responses.resource_not_found'));
            }

            $children = $companyDepartment->children()
                ->with(['department:id,name,code', 'manager:id,name,lastname'])
                ->get();

            return $this->successResponse($children);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, CompanyDepartment $companyDepartment): JsonResponse
    {
        try {
            if ($companyDepartment->company_id !== $company->id) {
                return $this->responseNotFound(__('responses.resource_not_found'));
            }

            $companyDepartment->delete();

            return $this->successResponse(null, __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
