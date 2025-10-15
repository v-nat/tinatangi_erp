@extends('layouts.modules-layouts.operations-screens-layout')
@section('title') Kitchen Display System | Tinatangi Cafe @endsection
@section('topTitle') Tinatangi Cafe | Kitchen Display @endsection
@section('screen') KDS @endsection
@section('posTopNav') d-none @endsection
@section('content')

    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="kdsOrders" class="display table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        {{-- DataTables will auto-generate headers based on JS config --}}
                    </table>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('scripts')
    <script src="{{ asset('source/laravel_echo/pusher.min.js') }}"></script>
    <script src="{{ asset('source/laravel_echo/echo.iife.js') }}"></script>
    <script>
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true
        });
    </script>
    <script src="{{ asset('js/kitchenDisplay.js') }}"></script>
@endsection
