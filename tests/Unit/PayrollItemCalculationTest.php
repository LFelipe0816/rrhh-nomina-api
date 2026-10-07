<?php

namespace Tests\Unit;

use App\Models\PayrollItem;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;
use Tests\TestCase;

class PayrollItemCalculationTest extends TestCase
{
    private function item(array $attributes): PayrollItem
    {
        return new PayrollItem($attributes);
    }

    private function call(PayrollItem $item, string $method): float
    {
        return (new ReflectionMethod($item, $method))->invoke($item);
    }

    public function test_gross_pay_includes_overtime_and_bonus(): void
    {
        // 32,000 / 160 h = 200 por hora; 8 h extra x 1.5 = 2,400
        $item = $this->item([
            'base_salary' => 32000,
            'overtime_hours' => 8,
            'overtime_rate' => 1.5,
            'bonus' => 1000,
        ]);

        $this->assertEqualsWithDelta(35400.00, $this->call($item, 'calculateGrossPay'), 0.001);
    }

    public function test_overtime_rate_defaults_to_one_and_a_half(): void
    {
        $withDefault = $this->item(['base_salary' => 16000, 'overtime_hours' => 10]);
        $explicit = $this->item(['base_salary' => 16000, 'overtime_hours' => 10, 'overtime_rate' => 1.5]);

        $this->assertEqualsWithDelta(
            $this->call($explicit, 'calculateGrossPay'),
            $this->call($withDefault, 'calculateGrossPay'),
            0.001
        );
    }

    public function test_total_deductions_add_percentage_tax_and_fixed_amounts(): void
    {
        $item = $this->item([
            'base_salary' => 32000,
            'overtime_hours' => 8,
            'overtime_rate' => 1.5,
            'bonus' => 1000,
            'tax_percentage' => 10,
            'social_security' => 1000,
            'other_deductions' => 200,
            'deductions' => 100,
        ]);

        // 10% de 35,400 = 3,540; + 1,000 + 200 + 100
        $this->assertEqualsWithDelta(4840.00, $this->call($item, 'calculateTotalDeductions'), 0.001);
    }

    #[DataProvider('withoutExtras')]
    public function test_salary_only_has_no_overtime_or_bonus(float $salary): void
    {
        $item = $this->item(['base_salary' => $salary]);

        $this->assertEqualsWithDelta($salary, $this->call($item, 'calculateGrossPay'), 0.001);
        $this->assertEqualsWithDelta(0.0, $this->call($item, 'calculateTotalDeductions'), 0.001);
    }

    public static function withoutExtras(): array
    {
        return [[0.0], [25000.0], [80000.5]];
    }
}
