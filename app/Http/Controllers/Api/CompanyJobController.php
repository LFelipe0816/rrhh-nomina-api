<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyJobRequest;
use App\Http\Requests\UpdateCompanyJobRequest;
use App\Models\Company;
use App\Models\CompanyJob;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CompanyJobController extends Controller
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

            $companyJobs = CompanyJob::where('company_id', $company->id)
                ->with(['jobLevel:id,name,code', 'department:id,name', 'boss:id,name'])
                ->when($request->boolean('active_only'), fn ($q) => $q->where('active', true))
                ->when($request->filled('department_id'), fn ($q) => $q->where('department_id', $request->query('department_id')))
                ->when(
                    $request->filled('search'),
                    fn (Builder $q) => $q->where('name', 'like', '%' . $request->query('search') . '%')
                )
                ->orderByDesc('id')
                ->paginate($perPage);

            return $this->successResponse($companyJobs);
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
            $companyJobs = CompanyJob::where('company_id', $company->id)
                ->with(['jobLevel:id,name,code', 'department:id,name', 'boss:id,name'])
                ->when($request->boolean('active_only'), fn ($q) => $q->where('active', true))
                ->when($request->filled('department_id'), fn ($q) => $q->where('department_id', $request->query('department_id')))
                ->orderBy('name')
                ->get();

            return $this->successResponse($companyJobs);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyJobRequest $request, Company $company): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['company_id'] = $company->id;

            $companyJob = CompanyJob::create($data);

            return $this->successResponse(
                $companyJob->load(['jobLevel:id,name,code', 'department:id,name', 'boss:id,name']),
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
    public function show(Company $company, CompanyJob $companyJob): JsonResponse
    {
        try {
            if ($companyJob->company_id !== $company->id) {
                return $this->responseNotFound(__('responses.resource_not_found'));
            }

            return $this->successResponse(
                $companyJob->load(['jobLevel:id,name,code', 'department:id,name', 'boss:id,name', 'subordinates:id,name,job_id'])
            );
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyJobRequest $request, Company $company, CompanyJob $companyJob): JsonResponse
    {
        try {
            if ($companyJob->company_id !== $company->id) {
                return $this->responseNotFound(__('responses.resource_not_found'));
            }

            $companyJob->update($request->validated());

            return $this->successResponse(
                $companyJob->fresh()->load(['jobLevel:id,name,code', 'department:id,name', 'boss:id,name']),
                __('responses.success_operation')
            );
        } catch (Exception|Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, CompanyJob $companyJob): JsonResponse
    {
        try {
            if ($companyJob->company_id !== $company->id) {
                return $this->responseNotFound(__('responses.resource_not_found'));
            }

            $companyJob->delete();

            return $this->successResponse(null, __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
