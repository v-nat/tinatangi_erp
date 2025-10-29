@component('mail::message')
# Notice of Salary Update

Hello {{ $employeeName }},

This is to inform you that the base salary for your position, **{{ $positionName }}**, has been updated.

Your new base monthly salary is now **₱{{ number_format($newBaseSalary, 2) }}**.

This change will be reflected in the upcoming payroll period.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
