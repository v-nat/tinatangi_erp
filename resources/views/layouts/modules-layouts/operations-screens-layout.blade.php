<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    @include('partials.app-head')
</head>

<body class="dark light">
    <div class="layout-navbar overflow-auto" style="max-height: 100vh; min-height: 90vh;">
        <nav class="navbar navbar-expand navbar-light">
            <div class="container-fluid">
                <div class="collapse navbar-collapse justify-content-start m-0">
                    <h3 class="card-title">@yield('topTitle')</h3>
                </div>
                <div class="collapse navbar-collapse justify-content-center d-none d-lg-flex">
                    @yield('navSearch')
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent">
                    <div class="w-100 d-lg-none mb-2">
                        @yield('navSearch')
                    </div>
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <div class="@yield('posTopNav')">
                            <li class="nav-item dropdown me-3">
                                <a class="nav-link active showTransactionsModal" href="#">
                                    <i class="fa-solid fa-receipt fs-4 text-gray-600"></i>
                                </a>
                            </li>
                        </div>
                    </ul>
                    <div class="dropdown">
                        <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-menu d-flex">
                                <div class="user-name text-end me-3">
                                    <h6 class="mb-0 text-gray-600">
                                        {{auth()->user()->full_name}}
                                    </h6>
                                    <p class="mb-0 text-sm text-gray-600">
                                        @if(auth()->user()->user_type == 'employee')
                                                                                <?php
                                            $userId = auth()->user()->id;
                                            $position = null;
                                            $userType = auth()->user()->user_type;

                                            switch ($userType) {
                                                case 'supplier':
                                                    break;

                                                case 'employee':
                                                    $employee = App\Models\Employee::where('user_id', $userId)->first();

                                                    if ($employee) {
                                                        $position = $employee->position->name;
                                                                                                                                                                                                                                                            ?>
                                                                                {{ \Illuminate\Support\Str::upper($position) }}
                                                                                <?php
                                                    }
                                                    break;

                                                default:
                                                    break;
                                            } ?>
                                        @endif
                                    </p>
                                </div>
                                <div class="user-img d-flex align-items-center">
                                    {{-- <div class="avatar avatar-md">
                                        <img src="assets/images/faces/1.jpg">
                                    </div> --}}
                                </div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            </li>
                            <li id="exit-pos-btn"><a class="dropdown-item" href="#">
                                    <i class="fa-solid fa-right-from-bracket"></i> End @yield('screen') Session</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="p-2 overflow-y-auto">
            @yield('content')
        </div>
    </div>
    @include('layouts.loading-state')

    @include('layouts.toast-swal')

    <script src="{{ asset('source/datatable/datatables.js') }}"></script>
    <script src="{{ asset('source/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    @yield('scripts')
</body>

</html>
