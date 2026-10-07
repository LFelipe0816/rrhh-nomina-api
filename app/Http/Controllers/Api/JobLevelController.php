<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobLevelRequest;
use App\Http\Requests\UpdateJobLevelRequest;
use App\Models\JobLevel;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class JobLevelController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->query('per_page', 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;

            $jobLevels = JobLevel::query()
                ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
                ->orderByDesc('id')
                ->paginate($perPage);

            return $this->successResponse($jobLevels);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Get all resources without pagination.
     */
    public function all(Request $request): JsonResponse
    {
        try {
            $jobLevels = JobLevel::query()
                ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
                ->orderBy('name')
                ->get();

            return $this->successResponse($jobLevels);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobLevelRequest $request): JsonResponse
    {
        try {
            $jobLevel = JobLevel::create($request->validated());

            return $this->successResponse($jobLevel, __('responses.success_operation'), 201);
        } catch (Exception|Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(JobLevel $jobLevel): JsonResponse
    {
        try {
            return $this->successResponse($jobLevel);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobLevelRequest $request, JobLevel $jobLevel): JsonResponse
    {
        try {
            $jobLevel->update($request->validated());

            return $this->successResponse($jobLevel->fresh(), __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobLevel $jobLevel): JsonResponse
    {
        try {
            $jobLevel->delete();

            return $this->successResponse(null, __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
