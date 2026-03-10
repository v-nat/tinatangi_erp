@extends('layouts.app')
@include('partials.crm-heading')
@section('crmAnnouncements') active @endsection
@section('headings') Announcement Management @endsection
@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('crm') }}">Customer Relationship</a></li>
            <li class="breadcrumb-item active" aria-current="page">Announcements</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h4 class="card-title mb-0">Announcements & Discounts</h4>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <label for="filter_type" class="form-label mb-0 text-nowrap">Type:</label>
                            <select id="filter_type" class="form-select form-select-sm" style="width:140px">
                                <option value="">All Types</option>
                                <option value="announcement">Announcement</option>
                                <option value="discount">Discount</option>
                                <option value="voucher">Voucher</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="filter_status" class="form-label mb-0 text-nowrap">Status:</label>
                            <select id="filter_status" class="form-select form-select-sm" style="width:140px">
                                <option value="">All Statuses</option>
                                <option value="Displayed">Displayed</option>
                                <option value="Hidden">Hidden</option>
                            </select>
                        </div>
                        <button class="btn btn-danger btn-sm d-none" id="btn-batch-delete-announcements">
                            <i class="fa-solid fa-trash-can"></i> Delete Selected
                        </button>
                        <button class="btn btn-primary btn-sm" id="btn-add-announcement">
                            <i class="fa-solid fa-plus"></i> Add New
                        </button>
                        <button class="btn btn-outline-primary btn-sm" id="btn-refresh-announcements">
                            <i class="fa-solid fa-rotate"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="announcementsTable" class="table table-sm table-hover dataTable no-footer" style="width:100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Order</th>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Badge</th>
                                <th>Theme</th>
                                <th>Valid From</th>
                                <th>Valid Until</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.modals.announcement-modal')

@endsection

@section('scripts')
    @vite('resources/js/announcementManagement.js')
@endsection
