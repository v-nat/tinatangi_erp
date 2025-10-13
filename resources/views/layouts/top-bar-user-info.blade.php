<?php

$userId = auth()->user()->id;
$position = null;
$userType = auth()->user()->user_type;

switch ($userType) {
    case 'supplier':
        break;

    case 'employee':
        $employee = App\Models\Employee::where('id', $userId)->first();

        if ($employee) {
            $position = $employee->position;
        }
        break;

    default:
        break;
}
?>

@if($employee)
    <script type="module" src="{{ asset('js/employeeAttendance.js') }}"></script>
@endif
