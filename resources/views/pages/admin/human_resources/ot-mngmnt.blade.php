@extends('layouts.app')
@include('partials.human-resources-heading')
@section('appMngt')active @endsection
@section('appMngt2')active @endsection
@section('sbi3')active @endsection
@section('headings') Overtime Approval @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('hr.employees') }}">Employee Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Overtimes</li>
        </ol>
    </nav>
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Overtimes Table</h4>
                <button class="btn btn-outline-primary btn-sm" id="btn-refresh-overtimes">
                    <i class="fa-solid fa-rotate"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="overtime_table" class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Date</th>
                                <th>Time Start</th>
                                <th>Time End</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.modals.hr-ot-mngmnt-modal')
@endsection
@section('scripts')
    @vite('resources/js/overtimeMngt.js')
@endsection
