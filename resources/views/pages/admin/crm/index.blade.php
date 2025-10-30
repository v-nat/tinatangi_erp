@extends('layouts.app')
@include('partials.crm-heading')
@section('crmIndex') active @endsection
@section('headings') Customer Relationship Dashboard @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('crm') }}">Customer Relationship</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <div class="section p-6 mt-4">
        <h2>Customer Feedback</h2>

        <div id="feedback-container" class="row">
            <p>Loading feedback...</p>
        </div>

        <div id="pagination-links" class="mt-4 d-flex justify-content-center"></div>
    </div>

    @include('layouts.modals.crm-view-photo-modal')

    <style>
        .feedback-card {
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            padding: 20px;
            transition: box-shadow 0.3s ease;
        }

        .feedback-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .card-header h5 {
            margin: 0;
            color: #333;
        }

        .card-rating {
            font-size: 1.2rem;
            font-weight: bold;
            color: #ffc107;
        }

        .card-body p {
            color: #555;
        }

        .card-photo a {
            font-weight: 500;
        }

        .card-footer {
            font-size: 0.85rem;
            color: #777;
            border-top: 1px solid #e9ecef;
            padding-top: 10px;
            margin-top: 15px;
        }

        html[data-bs-theme=dark] .feedback-card {
            background-color: #2d3748 !important;
            border-color: #4a5568 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
            margin-bottom: 20px !important;
            padding: 20px !important;
            transition: box-shadow 0.3s ease !important;
        }

        html[data-bs-theme=dark] .feedback-card:hover {
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.4) !important;
        }

        html[data-bs-theme=dark] .card-header,
        .card-footer {
            border-color: #4a5568 !important;
        }

        html[data-bs-theme=dark] .card-header h5 {
            color: #edf2f7 !important;
        }

        html[data-bs-theme=dark] .card-body p {
            color: #cbd5e0 !important;
        }

        html[data-bs-theme=dark] .card-photo a {
            color: #63b3ed !important;
        }

        html[data-bs-theme=dark] .card-footer {
            color: #a0aec0 !important;
        }
    </style>
@endsection
@section('scripts')
    <script src="{{ asset('js/customerService.js') }}"></script>
@endsection
