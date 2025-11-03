<div class="modal fade text-left w-100" id="employeeAttendance" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel20" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel20">Employee Attendance</h4>
            </div>
            <div class="modal-body p-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-body text-center">
                            <h1 id="realtimeClock" class="text-muted mb-3 fs-1 fw-bold"></h1>
                            <form id="attendanceForm">
                                @csrf
                                <input type="hidden" name="employee_id" id="employeeIdInput"
                                    value="{{ Auth::user()->id ?? '' }}">

                                <p class="mt-4 fs-5">
                                    <span class="d-block d-md-inline me-md-5">
                                        Time In: <span id="timeInDisplay" class="text-success">--:--:--</span>
                                    </span>

                                    <span class="d-block d-md-inline">
                                        Time Out: <span id="timeOutDisplay" class="text-danger">--:--:--</span>
                                    </span>
                                </p>

                                <p class="text-muted fs-6 mt-2">
                                    Minutes Today: <span id="totalHours" class="text-primary">0 minutes</span>
                                </p>

                                <div class="row d-block mb-3">
                                    <div class="col-12 w-100">
                                        <button type="button" class="btn btn-lg btn-success col-12" id="timeInBtn">Time
                                            In</button>
                                    </div>
                                    <div class="col-12 w-100 mt-1">
                                        <button type="button" class="btn btn-lg btn-danger col-12"
                                            id="timeOutBtn">TimeOut</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="{{route('employee.attendance.list', ['id' => Auth::user()->id])}}" class="self-center">My Attendance Records</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
