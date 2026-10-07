<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payroll>
 */
class PayrollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $baseSalary = $this->faker->randomFloat(2, 30000, 150000);
        $bonus = $this->faker->randomFloat(2, 0, 5000);
        $deductions = $this->faker->randomFloat(2, 0, 2000);
        $grossPay = $baseSalary + $bonus;
        $netPay = $grossPay - $deductions;

        $payPeriodStart = $this->faker->dateTimeBetween('-1 year', 'now');
        $payPeriodEnd = (clone $payPeriodStart)->modify('+15 days');

        return [
            'employee_id' => Employee::factory(),
            'pay_period_start' => $payPeriodStart,
            'pay_period_end' => $payPeriodEnd,
            'base_salary' => $baseSalary,
            'overtime_pay' => $this->faker->randomFloat(2, 0, 1000),
            'bonus' => $bonus,
            'commissions' => $this->faker->randomFloat(2, 0, 2000),
            'allowances' => $this->faker->randomFloat(2, 0, 1000),
            'other_earnings' => 0,
            'tax' => $this->faker->randomFloat(2, 1000, 5000),
            'social_security' => $this->faker->randomFloat(2, 500, 2000),
            'health_insurance' => $this->faker->randomFloat(2, 500, 2000),
            'retirement_contribution' => $this->faker->randomFloat(2, 500, 2000),
            'other_deductions' => 0,
            'gross_pay' => $grossPay,
            'total_deductions' => $deductions,
            'net_pay' => $netPay,
            'payment_date' => $payPeriodEnd,
            'payment_method' => $this->faker->randomElement(['bank_transfer', 'check']),
            'payment_reference' => $this->faker->uuid(),
            'status' => 'paid',
            'notes' => $this->faker->sentence(),
        ];
    }
}
