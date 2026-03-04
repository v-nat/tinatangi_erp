@extends('layouts.app')
@include('partials.crm-heading')
@section('crmGovDiscounts') active @endsection
@section('headings') Government Discounts @endsection

@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('crm') }}">Customer Relationship</a></li>
            <li class="breadcrumb-item active" aria-current="page">Government Discounts</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Government-Mandated Discounts</h4>
                        <p class="text-muted mb-0 mt-1" style="font-size:.82rem">
                            Manage discount types available to cashiers at POS (Senior Citizen, PWD, etc.)
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-danger btn-sm d-none" id="btn-batch-delete-govd">
                            <i class="fa-solid fa-trash me-1"></i> Delete Selected
                        </button>
                        <button class="btn btn-primary btn-sm" id="btn-add-govd">
                            <i class="fa-solid fa-plus me-1"></i> Add Type
                        </button>
                        <button class="btn btn-outline-primary btn-sm" id="btn-refresh-govd">
                            <i class="fa-solid fa-rotate-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="govdTable" class="table table-sm table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all-govd"></th>
                                <th>Name</th>
                                <th>Discount %</th>
                                <th>Description / Legal Basis</th>
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

    {{-- ── Add / Edit Modal ── --}}
    <div class="modal fade" id="govDiscountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="govDiscountModalLabel">
                        <i class="fa-solid fa-id-card me-2" style="color:#cda45e"></i>
                        Add Discount Type
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="govdForm">
                    @csrf
                    <input type="hidden" id="govd_id">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="govd_name" name="name"
                                   maxlength="100" placeholder="e.g. Senior Citizen" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Discount Percentage <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="govd_percentage" name="percentage"
                                       min="1" max="100" step="1" placeholder="20" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description / Legal Basis <small class="text-muted">(optional)</small></label>
                            <input type="text" class="form-control" id="govd_description" name="description"
                                   maxlength="255" placeholder="e.g. Republic Act 9994">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="govd_is_active" name="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-govd">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/governmentDiscountManagement.js') }}"></script>
@endsection
