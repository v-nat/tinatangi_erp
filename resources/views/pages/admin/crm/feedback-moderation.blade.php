@extends('layouts.app')
@include('partials.crm-heading')
@section('crmFeedback') active @endsection
@section('headings') Service Feedback Management @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('crm') }}">Customer Relationship</a></li>
            <li class="breadcrumb-item active" aria-current="page">Service Feedback</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center">

                    <h4 class="card-title mb-0">Customer Feedback</h4>

                    <div class="d-flex align-items-center">
                        <button id="batchDeleteBtn" class="btn btn-danger btn-sm me-4" disabled style="display: none;">
                            <i class="fas fa-trash-alt me-1"></i> Batch Delete Selected
                        </button>
                        <div class="d-flex align-items-center me-3">
                            <label for="submitted_date_filter" class="form-label mb-0 me-2 flex-shrink-0">Filter by
                                Submitted Date:</label>
                            <input type="date" id="submitted_date_filter" class="form-control form-control-sm"
                                style="width: 170px;">
                        </div>

                        <div class="d-flex align-items-center">
                            <label for="status_filter" class="form-label mb-0 me-2 flex-shrink-0">Filter by Status:</label>
                            <select id="status_filter" class="form-select form-select-sm" style="width: 170px;">
                                <option value="">All Statuses</option>
                                <option value="Displayed">Displayed</option>
                                <option value="Hidden">Hidden</option>
                            </select>
                        </div>

                        <button class="btn btn-outline-primary btn-sm ms-3" id="btn-refresh-feedback">
                            <i class="fa-solid fa-rotate"></i> Refresh
                        </button>

                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="feedbackTable" class="table table-sm table-hover dataTable no-footer" style="width:100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Customer</th>
                                <th>Rating (Overall)</th>
                                <th>Comment</th>
                                <th>Details</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.modals.crm-modal')

    <style>
        .feedback-hidden {
            opacity: 0.8;
        }

        .feedback-displayed {
            opacity: 1;
        }

        .card-rating {
            font-size: 1.1rem;
            font-weight: bold;
            color: #ffc107;
        }

        .card-photo a {
            font-weight: 500;
        }

        .rating-details small {
            display: block;
            margin-bottom: 2px;
        }

        td {
            word-break: break-word;
        }

        html[data-bs-theme=dark] .card-photo a {
            color: #63b3ed !important;
        }
    </style>
@endsection

@section('scripts')
    @vite('resources/js/feedbackManagement.js')
@endsection
