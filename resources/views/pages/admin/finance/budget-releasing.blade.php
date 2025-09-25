@extends('layouts.app')
@section('title') Finance Risk Management @endsection
@section('sidebar-title') Finance Risk Management @endsection
@section('human_resources') d-none @endsection
@section('finance') d-block @endsection
@section('procurement') d-none @endsection
@section('financeBudgets') active
@endsection
@section('headings') Budget Releasing @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('finance.payroll') }}">Finance</a></li>
            <li class="breadcrumb-item active" aria-current="page">Budgets</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Approved Requests - Awaiting Budget Release</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="approvalTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Requested by</th>
                                <th>Requested at</th>
                                <th>Department</th>
                                <th>Notes</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Released Budget History</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="historyTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Release ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Department</th>
                                <th>Released by</th>
                                <th>Released at</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade text-left" id="RejectionConfirmation" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel120" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title white" id="myModalLabel120">
                        Budget Request Rejection
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>You are about to Reject this Budget Request</h6>
                    <form id="rejectionForm">
                        @csrf
                        <input type="hidden" name="request_id" id="rejectionRequestId">
                        <input type="hidden" name="release_id" id="rejectionReleaseId">
                        <div class="form-group">
                            <label for="rejectionNotes">Rejection Notes (Required)</label>
                            <textarea class="form-control" id="rejectionNotes" name="notes" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>
                    <button id="reject-btn-confirmed" type="button" class="btn btn-danger ml-1" data-bs-dismiss="modal">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Reject</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}   "></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}   "></script>
    {{--
    <script src="assets/vendors/apexcharts/apexcharts.js"></script> --}}
    <script src="{{ asset('assets/js/pages/dashboard.js') }}   "></script>

    <script src="{{ asset('assets/js/main2.js') }}   "></script>
    <script src="{{ asset('js/budgetRelease.js') }}   "></script>
@endsection