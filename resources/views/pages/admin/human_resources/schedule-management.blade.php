@extends('layouts.app')
@include('partials.human-resources-heading')
@section('emplMngt')active @endsection
@section('emplMngt2')active @endsection
@section('sbi5')active @endsection
@section('headings') Schedule Management @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('hr.dashboard') }}">Human Resources</a></li>
            <li class="breadcrumb-item active" aria-current="page">Schedule Management</li>
        </ol>
    </nav>

    <section class="section">
        <div class="row g-4">
            <div class="col-12 col-xl-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-3">Employees</h4>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" id="scheduleEmployeeSearch" class="form-control"
                                placeholder="Search by name">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="scheduleEmployeeList" role="listbox"
                            aria-describedby="scheduleEmployeeHelp">
                        </div>
                    </div>
                    <div class="card-footer small text-muted" id="scheduleEmployeeHelp">
                        Select an employee to view or update their schedule.
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <div>
                            <h4 class="card-title mb-1">Schedule Details</h4>
                            <p class="text-muted small mb-0" id="selectedEmployeeLabel">
                                Choose an employee to begin.
                            </p>
                        </div>
                        <span class="badge bg-light text-dark border" id="scheduleStatusBadge">No employee selected</span>
                    </div>
                    <div class="card-body">
                        <form id="scheduleManagementForm" novalidate>
                            @csrf
                            <input type="hidden" id="scheduleEmployeeId">
                            <input type="hidden" id="scheduleId">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="schedule_title" class="form-label">Shift Title (Optional)</label>
                                    <input type="text" class="form-control" id="schedule_title" name="schedule_title"
                                        placeholder="e.g., Opening Shift">
                                    <div class="invalid-feedback" id="schedule_title_error"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="schedule_color" class="form-label">Color (Optional)</label>
                                    <input type="color" class="form-control form-control-color" id="schedule_color"
                                        name="color" value="#3788D8" title="Choose a color">
                                    <div class="invalid-feedback" id="color_error"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    Days of Week
                                    <small class="text-muted">Select exactly 6 working days</small>
                                </label>
                                <div class="day-checkboxes d-flex flex-wrap gap-3">
                                    @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $index => $day)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]"
                                                id="schedule_day_{{ $index }}" value="{{ $index }}">
                                            <label class="form-check-label" for="schedule_day_{{ $index }}">{{ $day }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="invalid-feedback d-none" id="days_of_week_error">Please select exactly six
                                    working days.</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="schedule_time_in" class="form-label">Start Time</label>
                                    <input type="time" class="form-control" id="schedule_time_in" name="time_in" required>
                                    <div class="invalid-feedback" id="time_in_error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="schedule_time_out" class="form-label">End Time</label>
                                    <input type="time" class="form-control" id="schedule_time_out" name="time_out" required>
                                    <div class="invalid-feedback" id="time_out_error"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="schedule_description" class="form-label">Description (Optional)</label>
                                <textarea class="form-control" id="schedule_description" name="description" rows="3"
                                    placeholder="Add notes that will appear in the calendar tooltip."></textarea>
                                <div class="invalid-feedback" id="description_error"></div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary" id="saveScheduleBtn" disabled>Save
                                    Schedule</button>
                                <button type="button" class="btn btn-outline-secondary" id="resetScheduleBtn"
                                    disabled>Reset</button>
                                <button type="button" class="btn btn-outline-danger" id="deleteScheduleBtn"
                                    disabled>Remove Schedule</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between flex-wrap gap-3">
                        <div>
                            <h4 class="card-title mb-1">Schedule Preview</h4>
                            <p class="text-muted small mb-0">Use the calendar to verify the weekly pattern.</p>
                        </div>
                        <span class="badge bg-primary-subtle text-primary" id="calendarViewBadge">No schedule loaded</span>
                    </div>
                    <div class="card-body">
                        <div id="schedulePreviewCalendar" class="calendar-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @vite('resources/js/hrScheduleManager.js')
@endsection

