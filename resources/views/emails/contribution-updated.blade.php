@component('mail::message')
# Notice of Update to Mandatory Contributions

Hello,

Please be advised that the company-wide mandatory contributions have been updated. The new monthly amounts are as follows:

- **SSS:** ₱{{ number_format($contributions['sss'], 2) }}
- **PhilHealth:** ₱{{ number_format($contributions['philhealth'], 2) }}
- **Pag-IBIG:** ₱{{ number_format($contributions['pagibig'], 2) }}

These changes will be reflected in the upcoming payroll period.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
