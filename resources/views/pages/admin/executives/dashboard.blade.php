@extends('layouts.app')
@include('partials.human-resources-heading')
@section('executivesnDashboard') active @endsection
@section('headings') Executives Dashboard @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('executives') }}">Executives</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/hrDashboard.js') }}"></script>
@endsection
