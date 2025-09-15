@extends('layouts.app')
@section('title') Human Resources @endsection
@section('sidebar-title') Human Resources Management @endsection
@section('dsh')
@endsection
@section('emplMngt')@endsection
@section('emplMngt2')@endsection
@section('appMngt')active
@endsection
@section('appMngt2')active
@endsection
@section('sbi1')
@endsection
@section('sbi2')
@endsection
@section('sbi3')
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
                    <table class="table table-striped" id="leaves_table" style="width:100%">
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

    <!--warning theme Modal -->
    <div class="modal fade text-left" id="ApprovalConfirmation" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel120" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title white" id="myModalLabel120">
                        Leave Request Confirmation
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>
                        Are you sure you want to approve this Leave Request?
                    </h6>
                    <form id="approvalForm">
                        @csrf
                        <input type="hidden" name="overtime_id" id="approvalOvertimeId">
                        <div class="form-group">
                            <label for="approvalNotes">Approval Notes (Optional)</label>
                            <textarea class="form-control" id="approvalNotes" name="reason" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Close</span>
                    </button>

                    <button type="button" class="btn btn-warning ml-1" data-bs-dismiss="modal">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Accept</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--Danger theme Modal -->
    <div class="modal fade text-left" id="RejectionConfirmation" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel120" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title white" id="myModalLabel120">
                        Leave Request Rejection
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>You are about to Reject this Leave Request</h6>
                    <form id="rejectionForm">
                        @csrf
                        <input type="hidden" name="overtime_id" id="approvalOvertimeId">
                        <div class="form-group">
                            <label for="approvalNotes">Rejection Notes (Optional)</label>
                            <textarea class="form-control" id="approvalNotes" name="reason" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Close</span>
                    </button>
                    <button type="button" class="btn btn-danger ml-1" data-bs-dismiss="modal">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Accept</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}   "></script>

    {{--
    <script src="assets/vendors/apexcharts/apexcharts.js"></script> --}}
    <script src="{{ asset('assets/js/pages/dashboard.js') }}   "></script>

    <script src="{{ asset('assets/js/main2.js') }}   "></script>
    <script src="{{ asset('js/leaveMngt.js') }}"></script>

@endsection