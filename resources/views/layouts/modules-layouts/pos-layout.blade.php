<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    @include('partials.app-head')
</head>

<body class="dark light">

    <nav class="navbar navbar-expand navbar-light ">
        <div class="container-fluid">
            <a href="#" class="burger-btn d-block">
                <i class="bi bi-justify fs-3"></i>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">

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
                                                                        <script type="module" src="{{ asset('js/employeeAttendance.js') }}"></script>
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
                        <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="{{ route('op.dashboard') }}">
                                <i class="fa-solid fa-right-from-bracket"></i> End POS Session</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    @include('layouts.loading-state')
    @include('layouts.toast-swal')

    <script src="{{ asset('source/jquery/datatables.js') }}"></script>
    <script src="{{ asset('source/jquery/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/logout.js') }}"></script>
    @yield('scripts')
</body>

</html>
