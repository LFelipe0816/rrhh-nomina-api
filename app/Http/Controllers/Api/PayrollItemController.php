<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayrollItemRequest;
use App\Http\Requests\UpdatePayrollItemRequest;
use App\Models\Company;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Throwable;

class PayrollItemController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Company $company, PayrollPeriod $payrollPeriod)
    {
        try {
            if ($payrollPeriod->company_id !== $company->id) {
                return $this->responseNotFound('Payroll period not found in this company');
            }

            $perPage = (int) $request->query('per_page', 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;

            $items = $payrollPeriod->payrollItems()
                ->with('employee:id,name,lastname,email')
                ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->query('status')))
                ->when(
                    $request->filled('search'),
                    fn (Builder $query) => $query->whereHas('employee', function (Builder $builder) use ($request) {
                        $search = '%'.strtolower($request->query('search')).'%';
                        $builder->whereRaw('LOWER(name) LIKE ?', [$search])
                            ->orWhereRaw('LOWER(lastname) LIKE ?', [$search])
                            ->orWhereRaw('LOWER(email) LIKE ?', [$search]);
                    })
                )
                ->orderByDesc('id')
                ->paginate($perPage);

            return $this->successResponse($items);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Get all resources without pagination.
     */
    public function all(Request $request, Company $company, PayrollPeriod $payrollPeriod)
    {
        try {
            if ($payrollPeriod->company_id !== $company->id) {
                return $this->responseNotFound('Payroll period not found in this company');
            }

            $items = $payrollPeriod->payrollItems()
                ->with('employee:id,name,lastname,email')
                ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->query('status')))
                ->orderByDesc('id')
                ->get();

            return $this->successResponse($items);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePayrollItemRequest $request, Company $company, PayrollPeriod $payrollPeriod)
    {
        try {
            if ($payrollPeriod->company_id !== $company->id) {
                return $this->responseNotFound('Payroll period not found in this company');
            }

            $item = PayrollItem::create(array_merge(
                $request->validated(),
                ['payroll_period_id' => $payrollPeriod->id]
            ));

            return $this->successResponse($item->load('employee:id,name,lastname,email'), __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company, PayrollPeriod $payrollPeriod, PayrollItem $payrollItem)
    {
        try {
            if ($payrollPeriod->company_id !== $company->id) {
                return $this->responseNotFound('Payroll period not found in this company');
            }

            if ($payrollItem->payroll_period_id !== $payrollPeriod->id) {
                return $this->responseNotFound('Payroll item not found in this period');
            }

            $payrollItem->load('employee:id,name,lastname,email');

            return $this->successResponse($payrollItem);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePayrollItemRequest $request, Company $company, PayrollPeriod $payrollPeriod, PayrollItem $payrollItem)
    {
        try {
            if ($payrollPeriod->company_id !== $company->id) {
                return $this->responseNotFound('Payroll period not found in this company');
            }

            if ($payrollItem->payroll_period_id !== $payrollPeriod->id) {
                return $this->responseNotFound('Payroll item not found in this period');
            }

            $payrollItem->update($request->validated());

            return $this->successResponse($payrollItem->fresh()->load('employee:id,name,lastname,email'), __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, PayrollPeriod $payrollPeriod, PayrollItem $payrollItem)
    {
        try {
            if ($payrollPeriod->company_id !== $company->id) {
                return $this->responseNotFound('Payroll period not found in this company');
            }

            if ($payrollItem->payroll_period_id !== $payrollPeriod->id) {
                return $this->responseNotFound('Payroll item not found in this period');
            }

            $payrollItem->delete();

            return $this->successResponse(null, __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }
}
