<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Seeder;

class PayrollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employeeIds = Employee::pluck('id', 'email');

        $payrolls = [
            [
                'employee_email' => 'ana.martinez@example.com',
                'pay_period_start' => '2024-11-01',
                'pay_period_end' => '2024-11-30',
                'base_salary' => 150000,
                'overtime_pay' => 7500,
                'bonus' => 10000,
                'commissions' => 0,
                'allowances' => 5000,
                'other_earnings' => 0,
                'tax' => 22000,
                'social_security' => 4500,
                'health_insurance' => 3500,
                'retirement_contribution' => 3000,
                'other_deductions' => 2000,
                'payment_date' => '2024-12-05',
                'payment_method' => 'bank_transfer',
                'payment_reference' => 'BT-2024-1205-001',
                'status' => 'paid',
                'notes' => 'Incluye bono por desempeño Q4.',
            ],
            [
                'employee_email' => 'luis.gomez@example.com',
                'pay_period_start' => '2024-11-01',
                'pay_period_end' => '2024-11-30',
                'base_salary' => 95000,
                'overtime_pay' => 0,
                'bonus' => 5000,
                'commissions' => 2500,
                'allowances' => 3000,
                'other_earnings' => 0,
                'tax' => 12000,
                'social_security' => 2800,
                'health_insurance' => 2200,
                'retirement_contribution' => 1800,
                'other_deductions' => 1500,
                'payment_date' => '2024-12-05',
                'payment_method' => 'bank_transfer',
                'payment_reference' => 'BT-2024-1205-002',
                'status' => 'paid',
                'notes' => 'Incluye comisión por cierre de contrato.',
            ],
            [
                'employee_email' => 'carla.perez@example.com',
                'pay_period_start' => '2024-11-01',
                'pay_period_end' => '2024-11-30',
                'base_salary' => 80000,
                'overtime_pay' => 3200,
                'bonus' => 0,
                'commissions' => 0,
                'allowances' => 2500,
                'other_earnings' => 0,
                'tax' => 9500,
                'social_security' => 2400,
                'health_insurance' => 2100,
                'retirement_contribution' => 1600,
                'other_deductions' => 1200,
                'payment_date' => '2024-12-05',
                'payment_method' => 'bank_transfer',
                'payment_reference' => 'BT-2024-1205-003',
                'status' => 'paid',
                'notes' => 'Incluye horas extras por capacitación.',
            ],
        ];

        foreach ($payrolls as $payroll) {
            $employeeId = $employeeIds[$payroll['employee_email']] ?? null;

            if (! $employeeId) {
                continue;
            }

            Payroll::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'pay_period_start' => $payroll['pay_period_start'],
                    'pay_period_end' => $payroll['pay_period_end'],
                ],
                array_merge(
                    collect($payroll)->except('employee_email')->toArray(),
                    ['employee_id' => $employeeId]
                )
            );
        }
    }
}
