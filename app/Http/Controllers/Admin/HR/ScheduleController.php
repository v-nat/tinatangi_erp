<?php

namespace App\Http\Controllers\Admin\HR;

use App\Helpers\MailSender;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class ScheduleController extends Controller
{
    public function getSchedules(Request $request)
    {
        $schedules = Schedule::with('employee.user')->get();

        $events = [];

        foreach ($schedules as $schedule) {
            $events[] = [
                'id'           => $schedule->id,
                'title'        => $schedule->title ?: ($schedule->employee->user->name ?? 'Scheduled'),
                'daysOfWeek'   => $schedule->days_of_week,
                'startTime'    => $schedule->time_in,
                'endTime'      => $schedule->time_out,
                'color'        => $schedule->color,
                'extendedProps' => [
                    'employee_id' => $schedule->employee_id,
                    'description' => $schedule->description,
                ],
                // You might need 'startRecur' and 'endRecur' if shifts have specific date ranges
                // 'startRecur' => '2025-11-01',
                // 'endRecur' => '2026-05-01',
            ];
        }

        return response()->json($events);
    }

    public function viewEmployeeSchedule(string $id)
    {
        if ((string) Auth::id() !== (string) $id && Auth::user()?->user_type === 'employee') {
            abort(403);
        }

        return view('pages.admin.human_resources.employee-schedule', compact('id'));
    }

    public function getEmployeeScheduleEvents(string $id)
    {
        if ((string) Auth::id() !== (string) $id && Auth::user()?->user_type === 'employee') {
            abort(403);
        }

        $schedules = Schedule::where('employee_id', $id)->get();

        $events = $schedules->map(function (Schedule $schedule) {
            $daysOfWeek = collect($schedule->days_of_week ?? [])
                ->map(fn($day) => (int) $day)
                ->filter(fn($day) => $day >= 0 && $day <= 6)
                ->values()
                ->toArray();

            $startTime = optional($schedule->time_in)->format('H:i');
            $endTime = optional($schedule->time_out)->format('H:i');

            $timeLabel = trim(sprintf('%s%s%s', $startTime, ($startTime && $endTime) ? ' - ' : '', $endTime));

            return [
                'id' => 'employee-schedule-' . $schedule->id,
                'title' => $schedule->title ?: 'Scheduled Shift',
                'daysOfWeek' => $daysOfWeek,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'display' => 'block',
                'color' => $schedule->color ?: '#3788D8',
                'textColor' => '#ffffff',
                'allDay' => false,
                'extendedProps' => [
                    'description' => $schedule->description,
                    'timeLabel' => $timeLabel,
                ],
            ];
        })->filter(fn($event) => !empty($event['daysOfWeek']))->values();

        return response()->json($events);
    }

    public function manage()
    {
        return view('pages.admin.human_resources.schedule-management');
    }

    public function employeesWithSchedules(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $employeesQuery = Employee::with(['user', 'position', 'deptRS', 'schedule'])
            ->whereHas('user')
            ->whereNotIn('department', [0, 1, '0', '1', 'Administration', 'Executives'])
            ->whereDoesntHave('deptRS', function ($query) {
                $query->whereIn('name', ['Administration', 'Executives']);
            });

        if ($search) {
            $employeesQuery->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $search . '%']);
                });
            });
        }

        $employees = $employeesQuery->get()
            ->sortBy(function (Employee $employee) {
                $lastName = strtolower($employee->user?->last_name ?? '');
                $firstName = strtolower($employee->user?->first_name ?? '');
                return $lastName . ' ' . $firstName;
            })
            ->values();

        $data = $employees->map(function (Employee $employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->user?->full_name ?? 'Unnamed Employee',
                'position' => $employee->position?->name,
                'department' => $employee->deptRS?->name,
                'schedule_id' => $employee->schedule?->id,
                'has_schedule' => $employee->schedule !== null,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function showEmployeeSchedule(Employee $employee): JsonResponse
    {
        $employee->loadMissing(['user', 'position', 'deptRS', 'schedule']);

        if ($this->isRestrictedDepartment($employee)) {
            abort(404);
        }

        return response()->json([
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->user?->full_name ?? 'Unnamed Employee',
                'position' => $employee->position?->name,
                'department' => $employee->deptRS?->name,
            ],
            'schedule' => $employee->schedule ? $this->formatScheduleResponse($employee->schedule) : null,
        ]);
    }

    public function upsertEmployeeSchedule(Request $request, Employee $employee): JsonResponse
    {
        if ($this->isRestrictedDepartment($employee)) {
            abort(404);
        }

        $validated = $this->validateSchedule($request);

        $days = collect($validated['days_of_week'])
            ->map(fn($day) => (int) $day)
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $attributes = [
            'title' => $validated['schedule_title'] ?? null,
            'days_of_week' => $days,
            'time_in' => $validated['time_in'] . ':00',
            'time_out' => $validated['time_out'] . ':00',
            'color' => $validated['color'] ?? null,
            'description' => $validated['description'] ?? null,
        ];

        $schedule = $employee->schedule()->updateOrCreate([], $attributes);

        $this->sendScheduleChangeNotification(
            $employee,
            $schedule,
            $schedule->wasRecentlyCreated ? 'created' : 'updated'
        );

        return response()->json([
            'message' => $schedule->wasRecentlyCreated ? 'Schedule created successfully.' : 'Schedule updated successfully.',
            'schedule' => $this->formatScheduleResponse($schedule),
        ], $schedule->wasRecentlyCreated ? 201 : 200);
    }

    public function destroySchedule(Schedule $schedule): JsonResponse
    {
        $schedule->loadMissing('employee.deptRS');

        if ($schedule->employee && $this->isRestrictedDepartment($schedule->employee)) {
            abort(404);
        }

        $employee = $schedule->employee;

        $schedule->delete();

        if ($employee) {
            $this->sendScheduleChangeNotification($employee, $schedule, 'removed');
        }

        return response()->json([
            'message' => 'Schedule removed successfully.',
        ]);
    }

    protected function validateSchedule(Request $request): array
    {
        return $request->validate([
            'schedule_title' => ['nullable', 'string', 'max:120'],
            'days_of_week' => ['required', 'array', 'size:6'],
            'days_of_week.*' => ['required', 'integer', Rule::in(range(0, 6))],
            'time_in' => ['required', 'date_format:H:i'],
            'time_out' => ['required', 'date_format:H:i', 'after:time_in'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'days_of_week.size' => 'Select exactly six working days.',
        ]);
    }

    protected function formatScheduleResponse(Schedule $schedule): array
    {
        $startTime = optional($schedule->time_in)->format('H:i');
        $endTime = optional($schedule->time_out)->format('H:i');

        return [
            'id' => $schedule->id,
            'title' => $schedule->title,
            'days_of_week' => collect($schedule->days_of_week ?? [])
                ->map(fn($day) => (int) $day)
                ->filter(fn($day) => $day >= 0 && $day <= 6)
                ->values()
                ->toArray(),
            'time_in' => $startTime,
            'time_out' => $endTime,
            'color' => $schedule->color ?? '#3788D8',
            'description' => $schedule->description,
            'time_label' => trim(sprintf('%s%s%s', $startTime, ($startTime && $endTime) ? ' - ' : '', $endTime)),
        ];
    }

    protected function isRestrictedDepartment(Employee $employee): bool
    {
        $departmentId = $employee->department;
        $departmentName = strtolower($employee->deptRS?->name ?? '');

        return in_array((int) $departmentId, [0, 1], true)
            || in_array($departmentName, ['administration', 'executives'], true);
    }

    protected function sendScheduleChangeNotification(Employee $employee, Schedule $schedule, string $changeType): void
    {
        if (!$employee->relationLoaded('user')) {
            $employee->load('user');
        }

        $content = $this->buildScheduleEmailContent($employee, $schedule, $changeType);

        if (empty($content['email'])) {
            return;
        }

        try {
            MailSender::sendEmployeeEmail($content);
        } catch (Throwable $exception) {
            Log::warning('Failed to send schedule change email', [
                'employee_id' => $employee->id,
                'schedule_id' => $schedule->id,
                'change_type' => $changeType,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    protected function buildScheduleEmailContent(Employee $employee, Schedule $schedule, string $changeType): array
    {
        $user = $employee->user;
        $email = $user?->email;

        if (!$email) {
            return [];
        }

        $titles = [
            'created' => 'New Work Schedule Assigned',
            'updated' => 'Work Schedule Updated',
            'removed' => 'Work Schedule Removed',
        ];

        $headlines = [
            'created' => 'You have been assigned a new work schedule.',
            'updated' => 'Your work schedule has been updated.',
            'removed' => 'Your work schedule has been removed.',
        ];

        return [
            'email' => $email,
            'title' => $titles[$changeType] ?? $titles['updated'],
            'name' => trim($user?->full_name ?? trim(($user?->first_name ?? '') . ' ' . ($user?->last_name ?? ''))),
            'headline' => $headlines[$changeType] ?? $headlines['updated'],
            'changeType' => $changeType,
            'scheduleTitle' => $schedule->title ?: 'Scheduled Shift',
            'daySummary' => $this->formatDaySummary($schedule->days_of_week ?? []),
            'timeRange' => $this->formatTimeRange($schedule->time_in, $schedule->time_out),
            'description' => $schedule->description,
            'actionUrl' => route('hr.employee-schedule', $employee->id),
            'blade_file' => 'emails.schedule-updated',
        ];
    }

    protected function formatDaySummary($days): string
    {
        $dayMap = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        $days = is_array($days) ? $days : (array) $days;
        $names = [];

        foreach ($days as $day) {
            $index = is_numeric($day) ? (int) $day : null;
            if ($index !== null && array_key_exists($index, $dayMap)) {
                $names[] = $dayMap[$index];
            }
        }

        $names = array_values(array_unique($names));

        if (empty($names)) {
            return 'No working days specified.';
        }

        if (count($names) === 7) {
            return 'Sunday through Saturday';
        }

        if (count($names) === 1) {
            return $names[0];
        }

        $last = array_pop($names);

        return implode(', ', $names) . ' and ' . $last;
    }

    protected function formatTimeRange(?string $start, ?string $end): string
    {
        $startFormatted = $this->formatTime($start);
        $endFormatted = $this->formatTime($end);

        if ($startFormatted && $endFormatted) {
            return $startFormatted . ' - ' . $endFormatted;
        }

        if ($startFormatted) {
            return $startFormatted;
        }

        if ($endFormatted) {
            return $endFormatted;
        }

        return 'No timeframe specified.';
    }

    protected function formatTime(?string $time): ?string
    {
        if ($time instanceof CarbonInterface) {
            return $time->format('g:i A');
        }

        if ($time instanceof \DateTimeInterface) {
            return Carbon::instance($time)->format('g:i A');
        }

        if (is_string($time)) {
            $time = trim($time);
            if ($time === '') {
                return null;
            }

            foreach (['H:i:s', 'H:i'] as $format) {
                try {
                    return Carbon::createFromFormat($format, $time)->format('g:i A');
                } catch (\Exception $e) {
                    continue;
                }
            }

            try {
                return Carbon::parse($time)->format('g:i A');
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}
