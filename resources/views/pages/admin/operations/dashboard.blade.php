@extends('layouts.app')
@include('partials.operations-heading')
@section('operationsIndex') active
@endsection
@section('headings') Operations @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('op.dashboard')}}">Operations</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
@endsection
@section('scripts')
    
@endsection
