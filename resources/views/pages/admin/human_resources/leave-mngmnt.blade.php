@extends('layouts.app')
@include('partials.human-resources-heading')
@section('emplMngt2')@endsection
@section('appMngt')active
@endsection
@section('appMngt2')active
@endsection
@section('sbi4')active
@endsection
@section('headings') Leave Approval @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('hr.employees') }}">Employee Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Leaves</li>
        </ol>
    </nav>
    <section class="section">
        <div class="card">
            <div class="card-header">
                Leaves Table
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="leaves_table" class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Date Start</th>
                                <th>Date End</th>
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

    @include('layouts.modals.hr-leave-mngmnt-modal')
@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/leaveMngt.js') }}"></script>
@endsection
