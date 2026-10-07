<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayrollPeriodRequest;
use App\Http\Requests\UpdatePayrollPeriodRequest;
use App\Models\Company;
use App\Models\PayrollPeriod;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Throwable;

class PayrollPeriodController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Company $company)
    {
        try {
            $perPage = (int) $request->query('per_page', 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;

            $periods = $company->payrollPeriods()
                ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->query('status')))
                ->when(
                    $request->filled('search'),
                    fn (Builder $query) => $query->where(function (Builder $builder) use ($request) {
                        $search = '%'.strtolower($request->query('search')).'%';
                        $builder->whereRaw('LOWER(name) LIKE ?', [$search]);
                    })
                )
                ->withCount('payrollItems')
                ->orderByDesc('id')
                ->paginate($perPage);

            return $this->successResponse($periods);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Get all resources without pagination.
     */
    public function all(Request $request, Company $company)
    {
        try {
            $periods = $company->payrollPeriods()
                ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->query('status')))
                ->withCount('payrollItems')
                ->orderByDesc('id')
                ->get();

            return $this->successResponse($periods);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePayrollPeriodRequest $request, Company $company)
    {
        try {
            $period = PayrollPeriod::create(array_merge(
                $request->validated(),
                ['company_id' => $company->id]
            ));

            return $this->successResponse($period, __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company, PayrollPeriod $payrollPeriod)
    {
        try {
            if ($payrollPeriod->company_id !== $company->id) {
                return $this->responseNotFound('Payroll period not found in this company');
            }

            $payrollPeriod->loadCount('payrollItems');

            return $this->successResponse($payrollPeriod);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePayrollPeriodRequest $request, Company $company, PayrollPeriod $payrollPeriod)
    {
        try {
            if ($payrollPeriod->company_id !== $company->id) {
                return $this->responseNotFound('Payroll period not found in this company');
            }

            $payrollPeriod->update($request->validated());

            return $this->successResponse($payrollPeriod->fresh(), __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, PayrollPeriod $payrollPeriod)
    {
        try {
            if ($payrollPeriod->company_id !== $company->id) {
                return $this->responseNotFound('Payroll period not found in this company');
            }

            $payrollPeriod->delete();

            return $this->successResponse(null, __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }
}
