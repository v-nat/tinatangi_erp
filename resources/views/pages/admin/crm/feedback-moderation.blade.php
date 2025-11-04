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

    <div class="section p-6 mt-4">
        <h2>Customer Feedback</h2>

        {{-- Filters and sorting controls remain the same --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="feedback-sort" class="form-label">Sort By</label>
                <select id="feedback-sort" class="form-select">
                    <option value="newest" selected>Date (Newest First)</option>
                    <option value="oldest">Date (Oldest First)</option>
                    <option value="rating-high">Rating (High to Low)</option>
                    <option value="rating-low">Rating (Low to High)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="feedback-filter" class="form-label">Filter Status</label>
                <select id="feedback-filter" class="form-select">
                    <option value="all" selected>Show All</option>
                    <option value="displayed">Only Displayed (35)</option>
                    <option value="hidden">Only Hidden (34)</option>
                </select>
            </div>
        </div>

        {{-- This is the new Table structure --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
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
                {{-- The 'feedback-container' is now the table body --}}
                <tbody id="feedback-container">
                    <tr>
                        <td colspan="7" class="text-center">
                            <p>Loading feedback...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="pagination-links" class="mt-4 d-flex justify-content-center"></div>
    </div>

    {{-- Modals remain the same --}}
    @include('layouts.modals.crm-modal')

    <style>
        /* Apply opacity to the whole table row */
        .feedback-hidden {
            opacity: 0.6;
        }

        .feedback-displayed {
            opacity: 1;
        }

        /* Keep the star rating color */
        .card-rating {
            font-size: 1.1rem;
            font-weight: bold;
            color: #ffc107;
        }

        /* Keep the photo link style */
        .card-photo a {
            font-weight: 500;
        }

        /* Style for the detailed ratings in the table cell */
        .rating-details small {
            display: block;
            margin-bottom: 2px;
        }

        /* Helper for long comments */
        td {
            word-break: break-word;
        }

        /* Dark mode link color */
        html[data-bs-theme=dark] .card-photo a {
            color: #63b3ed !important;
        }
    </style>
@endsection

@section('scripts')
    {{-- This JS file path remains the same --}}
    <script src="{{ asset('js/feedbackManagement.js') }}"></script>
@endsection
