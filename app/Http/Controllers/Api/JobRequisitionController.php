<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobRequisitionRequest;
use App\Http\Requests\UpdateJobRequisitionRequest;
use App\Models\Company;
use App\Models\CompanyJob;
use App\Models\JobRequisition;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class JobRequisitionController extends Controller
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

            $requisitions = JobRequisition::whereHas('companyJob', fn ($q) => $q->where('company_id', $company->id))
                ->with(['companyJob:id,name,job_id,department_id,company_id', 'companyJob.jobLevel:id,name', 'companyJob.department:id,name', 'requestingManager:id,name,lastname'])
                ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
                ->when($request->filled('job_id'), fn ($q) => $q->where('job_id', $request->query('job_id')))
                ->orderByDesc('id')
                ->paginate($perPage);

            return $this->successResponse($requisitions);
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
            $requisitions = JobRequisition::whereHas('companyJob', fn ($q) => $q->where('company_id', $company->id))
                ->with(['companyJob:id,name,job_id,department_id,company_id', 'companyJob.jobLevel:id,name', 'companyJob.department:id,name', 'requestingManager:id,name,lastname'])
                ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
                ->when($request->filled('job_id'), fn ($q) => $q->where('job_id', $request->query('job_id')))
                ->orderByDesc('id')
                ->get();

            return $this->successResponse($requisitions);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequisitionRequest $request, Company $company): JsonResponse
    {
        try {
            // Verificar que el job_id pertenece a la compañía
            $companyJob = CompanyJob::where('id', $request->job_id)
                ->where('company_id', $company->id)
                ->first();

            if (!$companyJob) {
                return $this->responseNotFound('El puesto de trabajo no pertenece a esta empresa');
            }

            $requisition = JobRequisition::create($request->validated());

            return $this->successResponse(
                $requisition->load(['companyJob:id,name,job_id,department_id,company_id', 'companyJob.jobLevel:id,name', 'requestingManager:id,name,lastname']),
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
    public function show(Company $company, JobRequisition $jobRequisition): JsonResponse
    {
        try {
            if ($jobRequisition->companyJob->company_id !== $company->id) {
                return $this->responseNotFound(__('responses.resource_not_found'));
            }

            return $this->successResponse(
                $jobRequisition->load(['companyJob:id,name,job_id,department_id,company_id', 'companyJob.jobLevel:id,name', 'companyJob.department:id,name', 'requestingManager:id,name,lastname'])
            );
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobRequisitionRequest $request, Company $company, JobRequisition $jobRequisition): JsonResponse
    {
        try {
            if ($jobRequisition->companyJob->company_id !== $company->id) {
                return $this->responseNotFound(__('responses.resource_not_found'));
            }

            $jobRequisition->update($request->validated());

            return $this->successResponse(
                $jobRequisition->fresh()->load(['companyJob:id,name,job_id,department_id,company_id', 'requestingManager:id,name,lastname']),
                __('responses.success_operation')
            );
        } catch (Exception|Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, JobRequisition $jobRequisition): JsonResponse
    {
        try {
            if ($jobRequisition->companyJob->company_id !== $company->id) {
                return $this->responseNotFound(__('responses.resource_not_found'));
            }

            $jobRequisition->delete();

            return $this->successResponse(null, __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
