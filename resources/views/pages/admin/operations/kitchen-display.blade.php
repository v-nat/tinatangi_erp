@extends('layouts.modules-layouts.operations-screens-layout')
{{-- ... other sections ... --}}
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
    {{-- Pusher and Echo CDNs (Order matters: Pusher first) --}}
    <script src="{{ asset('source/laravel_echo/pusher.min.js') }}"></script>
    <script src="{{ asset('source/laravel_echo/echo.iife.js') }}"></script>
    <script>
        // Echo Configuration (Reads .env keys)
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true
        });

        console.log('Laravel Echo environment configured.');
    </script>
    <script src="{{ asset('js/kitchenDisplay.js') }}"></script>
@endsection
