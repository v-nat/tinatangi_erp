@extends('layouts.app')
@include('partials.human-resources-heading')
@section('adminDashboard') active @endsection
@section('headings') Admin Dashboard @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">Administrator</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/hrDashboard.js') }}"></script>
@endsection
