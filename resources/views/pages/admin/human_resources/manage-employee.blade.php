@extends('layouts.hr-app')
@section('title') Human Resources @endsection
@section('sidebar-title') Human Resources Management @endsection
@section('dsh')
@endsection
@section('emplMngt')active
@endsection
@section('emplMngt2')active
@endsection
@section('sbi1')
@endsection
@section('sbi2')active
@endsection
@section('sbi3')
@endsection
@section('sbi4')
@endsection
@section('headings') Manage Employee @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('hr.employees') }}">Employee Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage</li>
        </ol>
    </nav>
    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <form id="employeeForm" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Personal Information</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="first_name">First Name *</label>
                                            <input type="text" id="first_name" class="form-control" placeholder="Taylor"
                                                name="first_name" required>
                                            @error('first_name')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="middle_name">Middle Name</label>
                                            <input type="text" id="middle_name" class="form-control" placeholder="Alison"
                                                name="middle_name">
                                            @error('middle_name')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="last_name">Last Name *</label>
                                            <input type="text" id="last_name" class="form-control" placeholder="Swift"
                                                name="last_name" required>
                                            @error('last_name')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">

                                            <label for="gender">Gender *</label>
                                            <select class="form-select" id="gender" name="gender">
                                                <option>Male</option>
                                                <option selected>Female</option>
                                                <option>Other</option>
                                            </select>
                                            @error('gender')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="birth_date">Birthdate *</label>
                                            <input type="date" id="birth_date" class="form-control" name="birth_date"
                                                placeholder="Dec-12-1989">
                                            @error('birth_date')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="age">Age</label>
                                            <input type="text" id="age" class="form-control" name="age" readonly
                                                placeholder="34">
                                            @error('age')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="address">Address *</label>
                                            <input type="text" id="address" class="form-control" placeholder="Tennessee"
                                                name="address">
                                            @error('address')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="postal_code">Postal Code </label>
                                            <input type="tel" id="postal_code" class="form-control" name="postal_code"
                                                placeholder="19611">
                                            @error('postal_code')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="email">Email *</label>
                                            <input type="email" id="email" class="form-control" name="email"
                                                placeholder="tswift@yahoo.com">
                                            @error('email')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="phone_number">Phone Number *</label>
                                            <input type="tel" id="phone_number" class="form-control" name="phone_number"
                                                pattern="^(09|\+639)\d{9}$" maxlength="11" minlength="11"
                                                placeholder="09121319890">
                                            @error('phone_number')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="citizenship">Citizenship</label>
                                            <input type="text" id="citizenship" class="form-control" name="citizenship"
                                                placeholder="American">
                                            @error('citizenship')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header mt-6">
                            <h4 class="card-title">Work Information</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="department">Department *</label>
                                            <select id="department" class="form-select" name="department" required>
                                                <option value="" disabled selected>Choose Department</option>
                                                @foreach ($departments as $department)
                                                    <option value="{{ $department->id }}">
                                                        {{ $department->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('department')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="position">Position *</label>
                                            <select class="form-select" id="position" name="position">
                                                <option>Supervisor</option>
                                                <option>Manager</option>
                                                <option selected>Staff</option>
                                            </select>
                                            @error('position')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="direct_supervisor">Direct Supervisor *</label>
                                            <select id="direct_supervisor" name="direct_supervisor" class="form-select">
                                                <option value="" disabled selected>Choose Supervisor</option>
                                                {{-- This will be populated dynamically --}}
                                            </select>
                                            @error('direct_supervisor')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header mt-6">
                            <h4 class="card-title">Salary Information</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="salary">Salary *</label>
                                            <input type="number" id="salary" class="form-control" placeholder="0.00"
                                                name="salary" required>
                                            @error('salary')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="sss">SSS *</label>
                                            <input type="number" id="sss" class="form-control" placeholder="0.00"
                                                name="sss">
                                            @error('sss')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="pagibig">Pag-ibig *</label>
                                            <input type="number" id="pagibig" class="form-control" placeholder="0.00"
                                                name="pagibig">
                                            @error('pagibig')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="philhealth">Philhealth *</label>
                                            <input type="number" id="philhealth" class="form-control" placeholder="0.00"
                                                name="philhealth">
                                            @error('philhealth')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 mt-3 d-flex justify-content-start">
                                        <button id="submit-btn-employee" class="btn btn-primary me-1 mb-1">Submit</button>
                                        <button id="reset" class="btn btn-light-secondary me-1 mb-1">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <div id="LoadingScreen"
        style="display: none; position: fixed; z-index: 9999; background: rgba(255,255,255,0.7); top: 0; left: 0; width: 100%; height: 100%;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}   "></script>
    {{--
    <script src="assets/vendors/apexcharts/apexcharts.js"></script> --}}
    <script src="{{ asset('assets/js/pages/dashboard.js') }}   "></script>

    <script src="{{ asset('assets/js/main2.js') }}   "></script>
    <script src="{{ asset('js/hrManage.js') }}"></script>
@endsection