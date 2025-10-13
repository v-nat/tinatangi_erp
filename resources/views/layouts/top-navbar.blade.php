<nav class="navbar navbar-expand navbar-light ">
    <div class="container-fluid">
        <a href="#" class="burger-btn d-block">
            <i class="bi bi-justify fs-3"></i>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown me-1">
                    <a class="nav-link active dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class='bi bi-envelope bi-sub fs-4 text-gray-600'></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li>
                            <h6 class="dropdown-header">Mail</h6>
                        </li>
                        <li><a class="dropdown-item" href="#">No new mail</a></li>
                    </ul>
                </li>
                <div class="@yield('topnavEmp')">
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link active showAttendanceModal" href="#">
                            <i class='bi bi-clock bi-sub fs-4 text-gray-600'></i>
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
                                                $position = $employee->position;
                                            ?>
                                            {{ \Illuminate\Support\Str::upper($positionName) }}
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
                    <li>
                        <h6 class="dropdown-header">Hello, {{ auth()->user()->first_name }}!</h6>
                    </li>
                    <div class="@yield('topnavEmp')">
                        <li><a class="dropdown-item showAttendanceModal" href="#"><i class="fa-solid fa-clock"></i>
                                Attendance</a></li>
                        <li><a class="dropdown-item"
                                href="{{route('hr.ot-application', ['id' => Auth::user()->id])}}"><i
                                    class="fa-solid fa-business-time"></i>
                                Apply Overtime</a></li>
                        <li><a class="dropdown-item"
                                href="{{route('hr.leave-application', ['id' => Auth::user()->id])}}"><i
                                    class="fa-solid fa-calendar-days"></i>
                                Apply Leave</a></li>
                    </div>

                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user"></i>
                            Prpfile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-gear"></i>
                            Settings</a></li>
                    <hr class="dropdown-divider">
                    </li>
                    <li id="logout-btn"><a class="dropdown-item" href="#">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
