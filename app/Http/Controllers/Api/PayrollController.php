<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayrollRequest;
use App\Http\Requests\UpdatePayrollRequest;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Throwable;

class PayrollController extends Controller
{
    use ApiResponse;

    public function index(Request $request, Company $company, Employee $employee)
    {
        try {
            $perPage = (int) $request->query('per_page', 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;

            $payrolls = $employee->payrolls()
                ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->query('status')))
                ->orderByDesc('payment_date')
                ->paginate($perPage);

            return $this->successResponse($payrolls);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    public function all(Request $request, Company $company, Employee $employee)
    {
        try {
            $payrolls = $employee->payrolls()
                ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->query('status')))
                ->orderByDesc('payment_date')
                ->get();

            return $this->successResponse($payrolls);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    public function store(StorePayrollRequest $request, Company $company, Employee $employee)
    {
        try {
            $payload = array_merge($request->validated(), [
                'employee_id' => $employee->id,
            ]);

            $payroll = $employee->payrolls()->create($payload);
            return $this->successResponse($payroll, __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    public function show(Company $company, Employee $employee, Payroll $payroll)
    {
        try {
            return $this->successResponse($payroll);
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    public function update(UpdatePayrollRequest $request, Company $company, Employee $employee, Payroll $payroll)
    {
        try {
            $payroll->update($request->validated());
            return $this->successResponse($payroll, __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    public function destroy(Company $company, Employee $employee, Payroll $payroll)
    {
        try {
            $payroll->delete();
            return $this->successResponse(null, __('responses.success_operation'));
        } catch (Exception|Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }
}
