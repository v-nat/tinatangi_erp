@extends('layouts.modules-layouts.pos-layout')
@section('title') Point of Sale System | Tinatangi Cafe @endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Javascript Behavior</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-3">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="v-pills-home-tab" data-bs-toggle="pill" href="#v-pills-home"
                            role="tab" aria-controls="v-pills-home" aria-selected="true">Home</a>
                        <a class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill" href="#v-pills-profile"
                            role="tab" aria-controls="v-pills-profile" aria-selected="false">Profile</a>
                        <a class="nav-link" id="v-pills-messages-tab" data-bs-toggle="pill" href="#v-pills-messages"
                            role="tab" aria-controls="v-pills-messages" aria-selected="false">Messages</a>
                        <a class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill" href="#v-pills-settings"
                            role="tab" aria-controls="v-pills-settings" aria-selected="false">Settings</a>
                    </div>
                </div>
                <div class="col-9">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel"
                            aria-labelledby="v-pills-home-tab">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla ut
                            nulla neque.
                            Ut hendrerit nulla a euismod pretium.
                            Fusce venenatis sagittis ex efficitur suscipit. In tempor mattis
                            fringilla. Sed
                            id tincidunt orci, et volutpat ligula.
                            Aliquam sollicitudin sagittis ex, a rhoncus nisl feugiat quis. Lorem
                            ipsum dolor
                            sit amet, consectetur adipiscing elit.
                            Nunc ultricies ligula a tempor vulputate. Suspendisse pretium mollis
                            ultrices
                        </div>
                        <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                            aria-labelledby="v-pills-profile-tab">
                            Integer interdum diam eleifend metus lacinia, quis gravida eros
                            mollis. Fusce
                            non sapien sit amet magna dapibus
                            ultrices. Morbi tincidunt magna ex, eget faucibus sapien bibendum
                            non. Duis a
                            mauris ex. Ut finibus risus sed massa
                            mattis porta. Aliquam sagittis massa et purus efficitur ultricies.
                        </div>
                        <div class="tab-pane fade" id="v-pills-messages" role="tabpanel"
                            aria-labelledby="v-pills-messages-tab">
                            Integer pretium dolor at sapien laoreet ultricies. Fusce congue et
                            lorem id
                            convallis. Nulla volutpat tellus nec
                            molestie finibus. In nec odio tincidunt eros finibus ullamcorper. Ut
                            sodales,
                            dui nec posuere finibus, nisl sem aliquam
                            metus, eu accumsan lacus felis at odio.
                        </div>
                        <div class="tab-pane fade" id="v-pills-settings" role="tabpanel"
                            aria-labelledby="v-pills-settings-tab">
                            Sed lacus quam, convallis quis condimentum ut, accumsan congue
                            massa.
                            Pellentesque et quam vel massa pretium ullamcorper
                            vitae eu tortor.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/pointOfSale.js') }}   "></script>
@endsection
