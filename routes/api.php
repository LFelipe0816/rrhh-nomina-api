<?php

use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CompanyDepartmentController;
use App\Http\Controllers\Api\CompanyJobController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\JobLevelController;
use App\Http\Controllers\Api\JobRequisitionController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\PayrollItemController;
use App\Http\Controllers\Api\PayrollPeriodController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->scopeBindings()->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/user', [AuthController::class, 'user']);
        Route::put('auth/profile', [AuthController::class, 'updateProfile']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::get('users', [AuthController::class, 'users']);

        Route::get('departments', [DepartmentController::class, 'allDepartments']);

        // Rutas para obtener todos los datos sin paginación
        Route::get('companies/all', [CompanyController::class, 'all']);
        Route::get('companies/{company}/company-departments/all', [CompanyDepartmentController::class, 'all']);
        Route::get('companies/{company}/employees/all', [EmployeeController::class, 'all']);
        Route::get('companies/{company}/employees/{employee}/payrolls/all', [PayrollController::class, 'all']);
        Route::get('companies/{company}/payroll-periods/all', [PayrollPeriodController::class, 'all']);
        Route::get('companies/{company}/payroll-periods/{payroll_period}/payroll-items/all', [PayrollItemController::class, 'all']);
        Route::get('job-levels/all', [JobLevelController::class, 'all']);
        Route::get('companies/{company}/company-jobs/all', [CompanyJobController::class, 'all']);
        Route::get('companies/{company}/job-requisitions/all', [JobRequisitionController::class, 'all']);

        // Job Levels (global, no depende de empresa)
        Route::apiResource('job-levels', JobLevelController::class);

        Route::apiResource('companies', CompanyController::class);

        // Company Jobs y Job Requisitions (dependen de empresa)
        Route::apiResource('companies.company-jobs', CompanyJobController::class);
        Route::apiResource('companies.job-requisitions', JobRequisitionController::class);

        // Rutas de jerarquía de departamentos por empresa
        Route::get('companies/{company}/company-departments/tree', [CompanyDepartmentController::class, 'tree']);
        Route::get('companies/{company}/company-departments/{company_department}/children', [CompanyDepartmentController::class, 'children']);
        Route::apiResource('companies.company-departments', CompanyDepartmentController::class);
        Route::apiResource('companies.departments', DepartmentController::class);
        Route::apiResource('companies.employees', EmployeeController::class);
        Route::apiResource('companies.employees.payrolls', PayrollController::class);
        Route::apiResource('companies.payroll-periods', PayrollPeriodController::class);
        Route::apiResource('companies.payroll-periods.payroll-items', PayrollItemController::class);

    });
});

