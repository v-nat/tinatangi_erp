@extends('layouts.app')
@include('partials.finance-accounting-heading')
@section('financeIndex') active @endsection
@section('headings') Finance and Accounting @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('finance.payroll') }}">Finance</a></li>
        </ol>
    </nav>
@endsection
@section('scripts')
@endsection