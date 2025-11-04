@extends('layouts.app')
@include('partials.finance-accounting-heading')
@section('financeIndex') active @endsection
@section('headings') Finance and Accounting @endsection

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/vendors/apexcharts/apexcharts.css') }}">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('finance') }}">Finance</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <section class="section row">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon purple rounded-lg">
                                <i class="iconly-boldWallet"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Total Payroll (Month)</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-total-payroll">Loading...</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon blue rounded-lg">
                                <i class="iconly-boldUpload"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Budget Released (Month)</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-budget-released">Loading...</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon green rounded-lg">
                                <i class="iconly-boldBuy"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">PO Spending (Month)</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-po-spending">Loading...</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon red rounded-lg">
                                <i class="iconly-boldTime-Circle"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Pending PRs</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-pending-prs">Loading...</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header">
                    <h4>Budget vs. Spending (Last 6 Months)</h4>
                </div>
                <div class="card-body">
                    <div id="chart-budget-spending"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header">
                    <h4>Spending by Department</h4>
                </div>
                <div class="card-body">
                    <div id="chart-spending-department"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="section row mb-4">
        <div class="col-md-7">
            <div class="card h-100 shadow-sm border-0 rounded-lg">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Pending Budget Releases</h4>
                    <a href="{{ route('finance.budgets') }}">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="table-pending-budgets">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data injected by JS --}}
                                <tr>
                                    <td colspan="2" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card h-100 shadow-sm border-0 rounded-lg">
                <div class="card-header">
                    <h4>Payroll Overview (Last 3 Periods)</h4>
                </div>
                <div class="card-body">
                    <div id="chart-payroll-overview"></div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    <script type="module" src="{{ asset('js/financeDashboard.js') }}"></script>
@endsection
