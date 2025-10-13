<?php

$userId = auth()->user()->id;
$position = null;
$userType = auth()->user()->user_type;

switch ($userType) {
    case 'supplier':
        break;

    case 'employee':
        $employee = App\Models\Employee::where('user_id', $userId)->first();

        if ($employee) {
            $position = $employee->position;
            ?>
            <script type="module" src="{{ asset('js/employeeAttendance.js') }}"></script>
            <?php
        }
        break;

    default:
        break;
}
