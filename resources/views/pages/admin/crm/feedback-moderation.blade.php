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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Customer Feedback</h4>

                    <div class="d-flex align-items-center" style="width: 320px;">
                        <label for="status_filter" class="form-label mb-0 me-2 flex-shrink-0">Filter by Status:</label>
                        <select id="status_filter" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="Displayed">Displayed</option>
                            <option value="Hidden">Hidden</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="submitted_date_filter" class="form-label mb-1">Filter by Submitted Date:</label>
                        <input type="date" id="submitted_date_filter" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="feedbackTable" class="table table-hover align-middle" style="width:100%">
                        <thead>
                            <tr>
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
            opacity: 0.6;
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
    <script type="module" src="{{ asset('js/feedbackManagement.js') }}"></script>
@endsection
