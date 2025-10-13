@extends('layouts.app')
@include('partials.human-resources-heading')
@section('emplMngt')active @endsection
@section('emplMngt2')active @endsection
@section('sbi1')active @endsection
@section('headings') Employee List @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('hr.dashboard') }}">Human Resources</a></li>
            <li class="breadcrumb-item active" aria-current="page">Employees</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Employee Table</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="employee_table" class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Email</th>
                                <th>Reporting Manager</th>
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

    @include('layouts.modals.hr-employees-modal')
    <style>
        .action-btns {
            display: flex;
            justify-content: center
        }
    </style>
@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/hrEmployees.js') }}"></script>
@endsection
