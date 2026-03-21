@extends('layouts.app')
@include('partials.human-resources-heading')
@section('emplMngt')active @endsection
@section('emplMngt2')active @endsection
@section('sbi2')active @endsection
@section('headings') {{$title}} Employee @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('hr.employees') }}">Employee Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
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
                                            <input type="text" id="first_name" class="form-control" placeholder="Enter Firstname"
                                                name="first_name" value="{{ old('first_name', $data['first_name'] ?? '') }}" required>
                                            @error('first_name')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback">First Name is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="middle_name">Middle Name</label>
                                            <input type="text" id="middle_name" class="form-control" placeholder="Optional"
                                                name="middle_name" value="{{ old('middle_name', $data['middle_name'] ?? '') }}">
                                            @error('middle_name')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="last_name">Last Name *</label>
                                            <input type="text" id="last_name" class="form-control" placeholder="Enter Lastname"
                                                name="last_name" value="{{ old('last_name', $data['last_name'] ?? '') }}" required>
                                            @error('last_name')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback">Last Name is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="gender">Gender *</label>
                                            <select class="form-select" id="gender" name="gender" required>
                                                <option value="" disabled selected>Choose Gender</option>
                                                <option value="Male" {{ old('gender', $data['gender'] ?? '') == 'Male' ? 'selected' : '' }}>Male
                                                </option>
                                                <option value="Female" {{ old('gender', $data['gender'] ?? '') == 'Female' ? 'selected' : '' }}>
                                                    Female
                                                </option>
                                                <option value="Preferred not to say" {{ old('gender', $data['gender'] ?? '') == 'Preferred not to say' ? 'selected' : '' }}>
                                                    Preferred not to say
                                                </option>
                                            </select>
                                            @error('gender')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback">Gender is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="birth_date">Birthdate *</label>
                                            <input type="date" id="birth_date" class="form-control" name="birth_date"
                                                placeholder="1989-12-13" value="{{ old('birth_date', optional($data['birth_date'] ?? null)->format('Y-m-d') ?? '') }}" required>
                                            @error('birth_date')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback">Birthdate is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="age">Age</label>
                                            <input type="text" id="age" class="form-control" name="age" readonly
                                                placeholder="00" value="{{ old('age', $data['age'] ?? '') }}">
                                            @error('age')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="address">Address *</label>
                                            <input type="text" id="address" class="form-control" placeholder="Enter Address" required
                                                name="address" value="{{ old('address', $data['address'] ?? '') }}">
                                            @error('address')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback">Address is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="postal_code">Postal Code *</label>
                                             <input type="tel" id="postal_code" class="form-control" name="postal_code" pattern="^\d{4}$" maxlength="4" required
                                                inputmode="numeric" placeholder="4114" value="{{ old('postal_code', $data['postal_code'] ?? '') }}">
                                            @error('postal_code')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback">Postal Code must be exactly 4 digits.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="email">Email *</label>
                                            <input type="email" id="email" class="form-control" name="email" data-value="{{ $id ?? '' }}"
                                                placeholder="example@mail.com" value="{{ old('email', $data['email'] ?? '') }}" required>
                                            @error('email')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback">Email is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="phone_number">Phone Number *</label>
                                            <input type="tel" id="phone_number" class="form-control" name="phone_number"
                                                pattern="^(09|\+639)\d{9}$" maxlength="11" minlength="11" required
                                                placeholder="09123456789" value="{{ old('phone_number', $data['phone_number'] ?? '') }}">
                                            @error('phone_number')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback">Phone Number must be a valid 11-digit PH number (09...).</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="citizenship">Citizenship *</label>
                                            <input type="text" id="citizenship" class="form-control" name="citizenship" required
                                                placeholder="Enter Citizenship" value="{{ old('citizenship', $data['citizenship'] ?? '') }}">
                                            @error('citizenship')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback">Citizenship is required.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header mt-6">
                            <h4 class="card-title">Oraganizational Information</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                     <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="department">Department *</label>
                                            <select id="department" class="form-select" name="department" required>
                                                <option value="" disabled selected>Choose Department</option>
                                                @isset($departments)
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->id }}" {{ old('department', $data['department'] ?? '') == $department->id ? 'selected' : '' }}>
                                                            {{ $department->name }}
                                                        </option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                            @error('department')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback">Department is required.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="supervisor">Direct Supervisor *</label>
                                            <select id="supervisor" name="supervisor_id" class="form-select" data-value="{{ $data['supervisor'] ?? '' }}|{{$data['supervisor_id'] ?? ''}}" required>
                                                <option value=""  disabled selected>Choose Supervisor</option>
                                            </select>
                                            @error('supervisor_id')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback">Direct Supervisor is required.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="position">Position *</label>
                                            <select class="form-select" id="position" name="position_id" data-value="{{$data['position'] ?? ''}}|{{$data['position_id'] ?? ''}}|{{$data['level'] ?? ''}}" required>
                                                <option value="" disabled selected>Choose Position</option>
                                            </select>
                                            @error('position_id')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback">Position is required.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="Level">Role</label>
                                            <input type="text" name="level" id="level" class="form-control" readonly placeholder="staff">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header mt-6">
                            <h4 class="card-title">Schedule Management</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="schedule_title" class="form-label">Shift Title (Optional)</label>
                                    <input type="text" class="form-control" id="schedule_title" name="schedule_title" placeholder="e.g., Opening Shift" value="{{ old('schedule_title', optional($data['schedule'] ?? null)->title ?? '') }}">
                                    <div class="invalid-feedback" id="schedule_title_error"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Days of Week (Select exactly 6 days)</label>
                                    <div class="day-checkboxes">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" id="day_0" value="0" @checked(in_array('0', old('days_of_week', (array) (optional($data['schedule'] ?? null)->days_of_week ?? []))))>
                                            <label class="form-check-label" for="day_0">Sun</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" id="day_1" value="1" @checked(in_array('1', old('days_of_week', (array) (optional($data['schedule'] ?? null)->days_of_week ?? []))))>
                                            <label class="form-check-label" for="day_1">Mon</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" id="day_2" value="2" @checked(in_array('2', old('days_of_week', (array) (optional($data['schedule'] ?? null)->days_of_week ?? []))))>
                                            <label class="form-check-label" for="day_2">Tue</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" id="day_3" value="3" @checked(in_array('3', old('days_of_week', (array) (optional($data['schedule'] ?? null)->days_of_week ?? []))))>
                                            <label class="form-check-label" for="day_3">Wed</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" id="day_4" value="4" @checked(in_array('4', old('days_of_week', (array) (optional($data['schedule'] ?? null)->days_of_week ?? []))))>
                                            <label class="form-check-label" for="day_4">Thu</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" id="day_5" value="5" @checked(in_array('5', old('days_of_week', (array) (optional($data['schedule'] ?? null)->days_of_week ?? []))))>
                                            <label class="form-check-label" for="day_5">Fri</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" id="day_6" value="6" @checked(in_array('6', old('days_of_week', (array) (optional($data['schedule'] ?? null)->days_of_week ?? []))))>
                                            <label class="form-check-label" for="day_6">Sat</label>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" id="days_of_week_error">Please select exactly 6 days.</div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="schedule_time_in" class="form-label">Start Time</label>
                                        <input type="time" class="form-control" id="schedule_time_in" name="time_in" placeholder="Time" required value="{{ old('time_in', optional($data['schedule'] ?? null)->time_in ? Carbon\Carbon::parse(optional($data['schedule'] ?? null)->time_in)->format('H:i') : '') }}">
                                        <div class="invalid-feedback" id="time_in_error"></div>
                                    </div>
                                    <div class="col">
                                        <label for="schedule_time_out" class="form-label">End Time</label>
                                        <input type="time" class="form-control" id="schedule_time_out" name="time_out" placeholder="Time" required value="{{ old('time_out', optional($data['schedule'] ?? null)->time_out ? Carbon\Carbon::parse(optional($data['schedule'] ?? null)->time_out)->format('H:i') : '') }}">
                                        <div class="invalid-feedback" id="time_out_error"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="schedule_color" class="form-label">Color (Optional)</label>
                                    <input type="color" class="form-control form-control-color" id="schedule_color" name="color" value="{{ old('color', optional($data['schedule'] ?? null)->color ?? '#3788D8') }}" title="Choose a color">
                                     <div class="invalid-feedback" id="color_error"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="schedule_description" class="form-label">Description (Optional)</label>
                                    <textarea class="form-control" id="schedule_description" name="description" rows="2">{{ old('description', optional($data['schedule'] ?? null)->description ?? '') }}</textarea>
                                     <div class="invalid-feedback" id="description_error"></div>
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
                                            <label for="base_salary">Base Salary</label>
                                            <input type="text" id="base_salary" class="form-control" placeholder="₱0.00"
                                                inputmode="decimal" name="base_salary" value="{{ old('base_salary', $data['base_salary'] ?? '') }}" readonly>
                                            @error('base_salary')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <label class="form-label fw-semibold">Mandatory Contribution Preview
                                            <small class="text-muted fw-normal">(active rates × base salary)</small>
                                        </label>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-1">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Contribution</th>
                                                        <th class="text-end">EE Rate</th>
                                                        <th class="text-end">ER Rate</th>
                                                        <th class="text-end">EE Amount</th>
                                                        <th class="text-end">ER Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>SSS</td>
                                                        <td class="text-end" id="prev-sss-ee-rate">—</td>
                                                        <td class="text-end" id="prev-sss-er-rate">—</td>
                                                        <td class="text-end" id="prev-sss-ee">—</td>
                                                        <td class="text-end" id="prev-sss-er">—</td>
                                                    </tr>
                                                    <tr>
                                                        <td>PhilHealth</td>
                                                        <td class="text-end" id="prev-ph-ee-rate">—</td>
                                                        <td class="text-end" id="prev-ph-er-rate">—</td>
                                                        <td class="text-end" id="prev-ph-ee">—</td>
                                                        <td class="text-end" id="prev-ph-er">—</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pag-IBIG</td>
                                                        <td class="text-end" id="prev-pi-ee-rate">—</td>
                                                        <td class="text-end" id="prev-pi-er-rate">—</td>
                                                        <td class="text-end" id="prev-pi-ee">—</td>
                                                        <td class="text-end" id="prev-pi-er">—</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <small class="text-muted">EE = Employee share (deducted from pay) &nbsp;|&nbsp; ER = Employer share (for record only)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-3 mb-3 d-flex justify-content-start">
                        <button id="insert-btn-employee" data-mode="{{$mode ?? 'add'}}" class="btn btn-primary me-1 mb-1">Add Employee</button>
                        <button id="edit-btn-employee" hidden class="btn btn-primary me-1 mb-1">Update Employee</button>
                        <button type="reset" id="reset" class="btn btn-light-secondary me-1 mb-1">Reset</button>
                        <button type="button" id="cancel" hidden class="btn btn-light-secondary me-1 mb-1">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
@section('scripts')
    @vite('resources/js/hrManage.js')
@endsection
