<!-- view modal-->
<div class="modal fade text-left w-100" id="viewPayroll" tabindex="-1" role="dialog" aria-labelledby="myModalLabel20"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel20">View Payroll</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body p-4">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Close</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left w-100" id="payrollSettings" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel20" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel20">Payroll Settings</h4>
            </div>
            <div class="modal-body p-4">
                <form id="payrollSettingsForm" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Contribution Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="sss">SSS</label>
                                        <input type="text" id="sss" class="form-control" placeholder="₱0.00"
                                            inputmode="decimal" name="sss">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="pagibig">Pag-ibig</label>
                                        <input type="text" id="pagibig" class="form-control" placeholder="₱0.00"
                                            inputmode="decimal" name="pagibig">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="philhealth">Philhealth</label>
                                        <input type="text" id="philhealth" class="form-control" placeholder="₱0.00"
                                            inputmode="decimal" name="philhealth">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Apply Contribution Changes</button>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Salary Rate per Position</h4>
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <label for="positionDepartmentFilter" class="form-label mb-0 me-1">Filter by
                                    Department:</label>
                                <select id="positionDepartmentFilter" class="form-select form-select-sm"
                                    style="width: auto;">
                                    <option value="">All</option>
                                </select>
                            </div>

                            <button type="button" id="addNewPositionBtn" class="btn btn-primary">
                                <i class="fa-solid fa-plus"></i> New Position
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="positionsTable" class="table table-hover dataTable no-footer"
                                style="width:100% !important; table-layout:fixed">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Position</th>
                                        <th>Base Salary</th>
                                        <th>Rate per Hour</th>
                                        <th>Rate per Day</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <span class="d-none d-sm-block">Close</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editSalarySettingModal" tabindex="-1" role="dialog" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Salary for: <span id="modal-position-name" class="fw-bold"></span></h5>
            </div>
            <div class="modal-body">
                <form id="editSalarySettingForm">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label for="edit_base_salary">Base Salary</label>
                        <input type="text" id="edit_base_salary" name="base_salary" class="form-control"
                            inputmode="decimal">
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_rate_per_hour">Rate per Hour</label>
                        <!-- Added readonly attribute -->
                        <input type="text" id="edit_rate_per_hour" name="rate_per_hour" class="form-control"
                            inputmode="decimal" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_rate_per_day">Rate per Day</label>
                        <!-- Added readonly attribute -->
                        <input type="text" id="edit_rate_per_day" name="rate_per_day" class="form-control"
                            inputmode="decimal" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editSalarySettingForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addPositionModal" tabindex="-1" role="dialog" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Position</h5>
            </div>
            <div class="modal-body">
                <form id="addPositionForm">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="add_position_name" class="form-label">Position Name</label>
                        <input type="text" id="add_position_name" name="name" class="form-control" required>
                        <div class="invalid-feedback">Position Name is required.</div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="add_department_id" class="form-label">Department</label>
                        <select id="add_department_id" name="department_id" class="form-select" required>
                            <option value="" disabled selected>Loading departments...</option>
                        </select>
                        <div class="invalid-feedback">Department is required.</div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="add_level" class="form-label">Level</label>
                        <select id="add_level" name="level" class="form-select" required>
                            <option value="staff">Staff</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="manager">Manager</option>
                        </select>
                        <div class="invalid-feedback">Position Level is required.</div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="add_base_salary" class="form-label">Base Salary</label>
                        <input type="text" id="add_base_salary" name="base_salary" class="form-control"
                            inputmode="decimal" required placeholder="₱0.00">
                        <div class="invalid-feedback">Base Salary is required.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="cancelAddPosition" type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Cancel</button>
                <button id="submitAddPosition" type="submit" form="addPositionForm" class="btn btn-primary">Create
                    Position</button>
            </div>
        </div>
    </div>
</div>
