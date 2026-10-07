<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PayrollPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::with('employees')->get();

        if ($companies->isEmpty()) {
            $this->command->warn('No companies found. Please run CompanySeeder first.');
            return;
        }

        $statuses = ['draft', 'processing', 'approved', 'paid', 'cancelled'];
        $frequencies = ['weekly', 'biweekly', 'monthly'];

        foreach ($companies as $company) {
            $employees = $company->employees;

            if ($employees->isEmpty()) {
                $this->command->warn("Company '{$company->name}' has no employees. Skipping.");
                continue;
            }

            // Create 12 payroll periods (one per month for the last year)
            $startDate = Carbon::now()->subMonths(11)->startOfMonth();

            for ($i = 0; $i < 12; $i++) {
                $periodStart = $startDate->copy()->addMonths($i);
                $periodEnd = $periodStart->copy()->endOfMonth();
                $paymentDate = $periodEnd->copy()->addDays(5);

                // Older periods are more likely to be paid/approved
                $statusIndex = $i < 10 ? rand(2, 3) : rand(0, 1);
                $status = $statuses[$statusIndex];

                $period = PayrollPeriod::create([
                    'company_id' => $company->id,
                    'name' => 'Nómina ' . $periodStart->translatedFormat('F Y'),
                    'start_date' => $periodStart->toDateString(),
                    'end_date' => $periodEnd->toDateString(),
                    'payment_date' => $status === 'paid' ? $paymentDate->toDateString() : null,
                    'frequency' => 'monthly',
                    'status' => $status,
                    'notes' => $this->generatePeriodNote($status),
                ]);

                // Create payroll items for each employee in this period
                foreach ($employees as $employee) {
                    $this->createPayrollItem($period, $employee);
                }
            }

            $this->command->info("Created 12 payroll periods for company: {$company->name}");
        }
    }

    private function createPayrollItem(PayrollPeriod $period, Employee $employee): void
    {
        $baseSalary = $employee->monthly_salary ?? rand(40000, 200000);
        
        // Random overtime hours (0-20 hours)
        $overtimeHours = rand(0, 100) > 60 ? rand(1, 20) : 0;
        $overtimeRate = 1.5;
        
        // Random bonus
        $bonus = rand(0, 100) > 80 ? rand(5000, 50000) : 0;
        
        // Fixed deductions (loans, advances, etc.)
        $deductions = rand(0, 100) > 70 ? rand(500, 10000) : 0;
        
        // Tax percentage based on salary bracket (simplified)
        $taxPercentage = $this->calculateTaxPercentage($baseSalary);
        
        // AFP (Pension) - 2.87% employee contribution
        $socialSecurity = round($baseSalary * 0.0287, 2);
        
        // Other deductions
        $otherDeductions = rand(0, 100) > 80 ? rand(500, 5000) : 0;

        PayrollItem::create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'base_salary' => $baseSalary,
            'overtime_hours' => $overtimeHours,
            'overtime_rate' => $overtimeRate,
            'bonus' => $bonus,
            'deductions' => $deductions,
            'tax_percentage' => $taxPercentage,
            'social_security' => $socialSecurity,
            'other_deductions' => $otherDeductions,
            'gross_pay' => 0, // Will be calculated by model
            'total_deductions' => 0, // Will be calculated by model
            'net_pay' => 0, // Will be calculated by model
            'notes' => $this->generateItemNote(),
        ]);
    }

    private function calculateTaxPercentage(float $baseSalary): float
    {
        // Simplified Dominican ISR brackets (monthly)
        $annualIncome = $baseSalary * 12;
        
        if ($annualIncome <= 416220) {
            return 0;
        } elseif ($annualIncome <= 624329) {
            return 15;
        } elseif ($annualIncome <= 867123) {
            return 20;
        } else {
            return 25;
        }
    }

    private function generatePeriodNote(string $status): ?string
    {
        $notes = [
            'paid' => [
                'Nómina procesada y pagada correctamente.',
                'Pago realizado vía transferencia bancaria.',
                'Período cerrado sin incidencias.',
                null,
            ],
            'approved' => [
                'Pendiente de pago.',
                'Revisión completada por RRHH.',
                'Esperando fondos para procesamiento.',
                null,
            ],
            'processing' => [
                'En proceso de cálculo de deducciones.',
                'Verificando horas extras reportadas.',
                'Pendiente validación de bonificaciones.',
            ],
            'draft' => [
                'Período en preparación.',
                'Borrador inicial creado.',
                null,
            ],
            'cancelled' => [
                'Período cancelado.',
                'Anulado por error en datos.',
                null,
            ],
        ];

        $options = $notes[$status] ?? [null];
        return $options[array_rand($options)];
    }

    private function generateItemNote(): ?string
    {
        $notes = [
            'Pago regular.',
            'Incluye horas extras.',
            'Con bonificación por desempeño.',
            'Ajuste de nómina aplicado.',
            null,
            null,
            null,
        ];

        return $notes[array_rand($notes)];
    }
}
