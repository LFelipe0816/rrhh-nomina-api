<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyDepartment;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Test User
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create 10 Companies
        $companies = Company::factory()->count(10)->create();

        // Create 20 Global Departments
        $departments = Department::factory()->count(20)->create();

        foreach ($companies as $company) {
            // Assign 5 random departments to this company
            $companyDepartments = $departments->random(5);

            foreach ($companyDepartments as $department) {
                // 1. Create Manager for this Department in this Company
                $manager = Employee::factory()->create([
                    'company_id' => $company->id,
                    'department_id' => $department->id,
                    'job_position' => 'Department Manager',
                ]);

                // 2. Attach Department to Company with Manager
                // Use attach instead of create on pivot model directly if we want standard way, 
                // but we need the 'active' and 'manager_id' fields.
                // Using syncWithoutDetaching or just attach.
                $company->departments()->attach($department->id, [
                    'manager_id' => $manager->id,
                    'active' => true,
                ]);

                // 3. Create 50 Regular Employees for this Department/Company
                $employees = Employee::factory()->count(50)->create([
                    'company_id' => $company->id,
                    'department_id' => $department->id,
                ]);

                // 4. Generate Payrolls for all employees in this department (Manager + Regulars)
                $allDepartmentEmployees = $employees->push($manager);

                foreach ($allDepartmentEmployees as $employee) {
                    Payroll::factory()->count(6)->create([
                        'employee_id' => $employee->id,
                    ]);
                }
            }
        }

        // 5. Generate Payroll Periods and Items for all companies
        $this->call(PayrollPeriodSeeder::class);
    }
}
