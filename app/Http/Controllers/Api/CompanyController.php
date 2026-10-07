<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class CompanyController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->query('per_page', 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;

            $companies = Company::select([
                'id',
                'name',
                'legal_name',
                'rnc',
                'email',
                'phone',
                'status',
            ])->withCount('departments')->paginate($perPage);
            return $this->successResponse($companies);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Get all resources without pagination.
     */
    public function all()
    {
        try {
            $companies = Company::select([
                'id',
                'name',
                'legal_name',
                'rnc',
                'email',
                'phone',
                'status',
            ])->withCount('departments')->get();

            return $this->successResponse($companies);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        try {
            $company = Company::create($request->validated());
            return $this->successResponse($company, __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        try {
            return $this->successResponse($company);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        try {
            $company->update($request->validated());
            return $this->successResponse($company->fresh(), __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        try {
            $company->delete();
            return $this->successResponse(null, __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }
}
