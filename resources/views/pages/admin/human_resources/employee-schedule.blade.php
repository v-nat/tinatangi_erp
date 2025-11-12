@extends('layouts.app')
@include('partials.general-employee-heading')
@section('headings') My Schedule @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#" onclick="history.back()">Employee</a></li>
            <li class="breadcrumb-item active" aria-current="page">Schedule</li>
        </ol>
    </nav>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex flex-wrap align-items-center gap-3">
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-0">Work Schedule</h4>
                            <p class="text-muted small mb-0">
                                Switch between month, week, or day views to see your upcoming shifts.
                            </p>
                        </div>
                        <span class="badge bg-primary text-wrap">Scheduled Shift</span>
                    </div>
                    <div class="card-body">
                        <div id="scheduleEmptyState" class="alert alert-light border text-center mb-3 d-none">
                            <strong>No schedule yet.</strong> We will notify you once a shift is assigned.
                        </div>
                        <div id="employeeScheduleCalendar" data-events-url="{{ route('hr.employee-schedule.events', $id) }}"
                            class="calendar-container">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

<style>
    #employeeScheduleCalendar {
        min-height: 450px;
    }

    .fc {
        font-size: 0.95rem;
    }

    [data-bs-theme="light"] .fc-theme-standard {
        --fc-page-bg-color: #ffffff;
        --fc-neutral-bg-color: #f8f9fa;
        --fc-border-color: rgba(33, 37, 41, 0.1);
        --fc-button-text-color: #0d6efd;
        --fc-button-bg-color: rgba(13, 110, 253, 0.1);
        --fc-button-border-color: rgba(13, 110, 253, 0.2);
        --fc-button-hover-bg-color: rgba(13, 110, 253, 0.15);
        --fc-button-hover-border-color: rgba(13, 110, 253, 0.35);
        --fc-button-active-bg-color: rgba(13, 110, 253, 0.2);
        --fc-button-active-border-color: rgba(13, 110, 253, 0.5);
    }

    [data-bs-theme="dark"] .fc-theme-standard {
        --fc-page-bg-color: var(--bs-body-bg);
        --fc-neutral-bg-color: rgba(255, 255, 255, 0.04);
        --fc-border-color: rgba(255, 255, 255, 0.12);
        --fc-button-text-color: #ffffff;
        --fc-button-bg-color: #4c83ff;
        --fc-button-border-color: #4c83ff;
        --fc-button-hover-bg-color: #3c6bdb;
        --fc-button-hover-border-color: #3c6bdb;
        --fc-button-active-bg-color: #335bc0;
        --fc-button-active-border-color: #335bc0;
        --fc-today-bg-color: rgba(76, 131, 255, 0.18);
    }

    [data-bs-theme="dark"] .fc .fc-toolbar-title,
    [data-bs-theme="dark"] .fc .fc-col-header-cell-cushion,
    [data-bs-theme="dark"] .fc .fc-daygrid-day-number,
    [data-bs-theme="dark"] .fc .fc-list-day-text,
    [data-bs-theme="dark"] .fc .fc-list-day-side-text,
    [data-bs-theme="dark"] .fc .fc-list-event-title,
    [data-bs-theme="dark"] .fc .fc-popover-header,
    [data-bs-theme="dark"] .fc .fc-list-event-time {
        color: rgba(255, 255, 255, 0.9);
    }

    [data-bs-theme="dark"] .fc .fc-list-event-dot {
        border-color: rgba(255, 255, 255, 0.75);
    }

    [data-bs-theme="dark"] .fc .fc-scrollgrid-section-sticky>* {
        background-color: rgba(18, 18, 18, 0.85);
        backdrop-filter: blur(6px);
    }

    [data-bs-theme="dark"] .fc .fc-daygrid-day.fc-day-today {
        background: rgba(76, 131, 255, 0.12);
    }

    [data-bs-theme="dark"] .fc .fc-list-empty {
        background-color: rgba(255, 255, 255, 0.04);
        color: rgba(255, 255, 255, 0.65);
    }

    @media (max-width: 767.98px) {
        #employeeScheduleCalendar .fc-toolbar.fc-header-toolbar {
            flex-wrap: wrap;
            gap: .75rem;
        }

        #employeeScheduleCalendar .fc-toolbar-title {
            font-size: 1.1rem;
        }
    }
</style>

@section('scripts')
    <script type="module" src="{{ asset('js/employeeSchedule.js') }}"></script>
@endsection
