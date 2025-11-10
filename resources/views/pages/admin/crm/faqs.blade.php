@extends('layouts.app')
@include('partials.crm-heading')
@section('crmFaqs') active @endsection
@section('headings') FAQ Management @endsection
@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('crm') }}">Customer Relationship</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage FAQs</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Frequently Asked Questions</h4>

                    <div class="d-flex gap-2">
                        <button class="btn btn-danger btn-sm d-none" id="btn-delete-selected">
                            <i class="fa-solid fa-trash-can"></i> Delete Selected
                        </button>
                        <button class="btn btn-primary btn-sm" id="btn-add-faq">
                            <i class="fa-solid fa-plus"></i> Add New FAQ
                        </button>
                        <button class="btn btn-outline-primary btn-sm" id="btn-refresh-faqs">
                            <i class="fa-solid fa-rotate"></i> Refresh
                        </button>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="faqsTable" class="table table-sm table-hover dataTable no-footer" style="width:100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Order</th>
                                <th>Question</th>
                                <th>Answer</th>
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

@endsection

@section('scripts')
    <script type="module" src="{{ asset('js/faqManagement.js') }}"></script>
@endsection
