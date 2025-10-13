@extends('layouts.app')
@include('partials.operations-heading')
@section('operationsPOS') active @endsection
@section('headings') Point Of Sale @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('op.dashboard')}}">Operations</a></li>
            <li class="breadcrumb-item active" aria-current="page">Point Of Sale</li>
        </ol>
    </nav>



@endsection

@section('scripts')
    <script type="module" src="{{ asset('js/pointOfSale.js') }}   "></script>
@endsection
