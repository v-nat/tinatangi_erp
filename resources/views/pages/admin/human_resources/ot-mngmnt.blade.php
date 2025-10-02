@extends('layouts.app')
@section('title') Human Resources @endsection
@section('sidebar-title') Human Resources Management @endsection
@section('human_resources') d-block @endsection
@section('finance') d-none @endsection
@section('procurement') d-none @endsection
@section('appMngt')active
@endsection
@section('appMngt2')active
@endsection
@section('sbi3')active
@endsection
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
            <div class="card-header">
                Overtimes Table
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
    <script src="{{ asset('js/overtimeMngt.js') }}"></script>
@endsection