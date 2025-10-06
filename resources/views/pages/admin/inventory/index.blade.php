@extends('layouts.app')
@include('partials.inventory-heading')
@section('inventoryIndex') active
@endsection
@section('headings') Inventory @endsection
@section('content')
<nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('inventory')}}">Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page"> </li>
        </ol>
    </nav>
@endsection
@section('scripts')
@endsection
