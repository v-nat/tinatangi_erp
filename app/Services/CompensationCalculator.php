<?php

namespace App\Services;

class CompensationCalculator
{
    /**
     * Accept raw payroll attributes, return computed fields.
     */
    public function fromPayrollAttributes(array $attrs): array
    {
        $days_absent_deduction = $attrs['days_absent_deduction'];
        $tardiness_deduction = $attrs['tardiness_deduction'];
        $mandatory_deduction = $attrs['mandatory_deduction']['total'];

        // Earnings before deductions
        $grossPay = $attrs['regular_hour_pay'] + $attrs['overtime_pay'];

        // dd($grossPay);

        // Salary subject to tax (deduct absence/tardiness/etc)
        $salaryBeforeTax = $grossPay
            - $days_absent_deduction
            - $tardiness_deduction
            - $mandatory_deduction;

        $tax = $this->taxAmount($grossPay - $days_absent_deduction - $tardiness_deduction - $mandatory_deduction);

        // Take-home after tax
        $netPay = $salaryBeforeTax - $tax;

        return [
            'gross_pay'         => round($grossPay, 2),
            'salary_before_tax' => round($salaryBeforeTax, 2),
            'net_pay'           => round($netPay, 2),
        ];
    }

    /**
     * Compute from an existing Payroll model instance.
     */
    public function fromPayroll(\App\Models\Payroll $payroll): array
    {
        return $this->fromPayrollAttributes($payroll->toArray());
    }

    protected function taxAmount($taxableIncome)
    {
        if ($taxableIncome <= 20833) {
            return 0;
        } elseif ($taxableIncome <= 33332) {
            return ($taxableIncome - 20833) * 0.20;
        } elseif ($taxableIncome <= 66666) {
            return 2500 + ($taxableIncome - 33333) * 0.25;
        } elseif ($taxableIncome <= 166666) {
            return 10833.33 + ($taxableIncome - 66667) * 0.30;
        } elseif ($taxableIncome <= 666666) {
            return 40833.33 + ($taxableIncome - 166667) * 0.32;
        } else {
            return 200833.33 + ($taxableIncome - 666667) * 0.35;
        }
    }
}
