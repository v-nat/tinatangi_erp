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

        {{--
          NOTE: The custom filter/sort dropdowns have been removed.
          DataTables provides its own search box and column-based sorting.
          If you need custom dropdown filters, they must be wired up
          to the DataTables API (e.g., table.column(n).search(val).draw()).
        --}}

        <div class="table-responsive">
            {{-- 1. Added id="feedbackTable" --}}
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
                {{-- 2. Emptied the tbody. DataTables will populate this. --}}
                <tbody>
                    {{-- Data will be loaded by DataTables --}}
                </tbody>
            </table>
        </div>

        {{-- 3. Removed the custom pagination div --}}

    </div>

    {{-- Modals remain the same --}}
    @include('layouts.modals.crm-modal')

    <style>
        /* These styles are still valid and will be used */
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
    {{-- Ensure you have DataTables JS loaded in your app layout --}}
    <script src="{{ asset('js/feedbackManagement.js') }}"></script>
@endsection
