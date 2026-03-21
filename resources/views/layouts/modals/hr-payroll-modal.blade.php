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
                <button id="hr-modal-print-btn" type="button" class="btn btn-primary ml-1 d-none">
                    <span class="d-none d-sm-block">Print Payslip</span>
                </button>
                <button id="hr-modal-release-btn" type="button" class="btn btn-success ml-1 d-none">
                    <span class="d-none d-sm-block">Release</span>
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
                            <h4 class="card-title">Contribution Rates</h4>
                            <p class="text-muted mb-0 small">Enter rates as percentages (e.g., 4.5 for 4.5%). Only employee shares are deducted from net pay. Employer shares are for record-keeping.</p>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="fw-semibold">SSS</label>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="sss_employee_rate">Employee Share (%)</label>
                                        <div class="input-group">
                                            <input type="number" id="sss_employee_rate" class="form-control"
                                                placeholder="0.00" step="0.01" min="0" max="100"
                                                name="sss_employee_rate">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="sss_employer_rate">Employer Share (%)</label>
                                        <div class="input-group">
                                            <input type="number" id="sss_employer_rate" class="form-control"
                                                placeholder="0.00" step="0.01" min="0" max="100"
                                                name="sss_employer_rate">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-2">
                                    <label class="fw-semibold">PhilHealth</label>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="philhealth_employee_rate">Employee Share (%)</label>
                                        <div class="input-group">
                                            <input type="number" id="philhealth_employee_rate" class="form-control"
                                                placeholder="0.00" step="0.01" min="0" max="100"
                                                name="philhealth_employee_rate">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="philhealth_employer_rate">Employer Share (%)</label>
                                        <div class="input-group">
                                            <input type="number" id="philhealth_employer_rate" class="form-control"
                                                placeholder="0.00" step="0.01" min="0" max="100"
                                                name="philhealth_employer_rate">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-2">
                                    <label class="fw-semibold">Pag-IBIG</label>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="pagibig_employee_rate">Employee Share (%)</label>
                                        <div class="input-group">
                                            <input type="number" id="pagibig_employee_rate" class="form-control"
                                                placeholder="0.00" step="0.01" min="0" max="100"
                                                name="pagibig_employee_rate">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="pagibig_employer_rate">Employer Share (%)</label>
                                        <div class="input-group">
                                            <input type="number" id="pagibig_employer_rate" class="form-control"
                                                placeholder="0.00" step="0.01" min="0" max="100"
                                                name="pagibig_employer_rate">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Apply Rate Changes</button>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Contribution Rate History</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="contributionHistoryTable" class="table table-sm table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Effective Date</th>
                                        <th colspan="2" class="text-center">SSS</th>
                                        <th colspan="2" class="text-center">PhilHealth</th>
                                        <th colspan="2" class="text-center">Pag-IBIG</th>
                                        <th>Set By</th>
                                        <th>Status</th>
                                    </tr>
                                    <tr class="small text-muted">
                                        <th></th>
                                        <th>EE %</th><th>ER %</th>
                                        <th>EE %</th><th>ER %</th>
                                        <th>EE %</th><th>ER %</th>
                                        <th></th><th></th>
                                    </tr>
                                </thead>
                                <tbody id="contributionHistoryBody">
                                    <tr><td colspan="9" class="text-center text-muted">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

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
                        <input type="text" id="edit_rate_per_hour" name="rate_per_hour" class="form-control"
                            inputmode="decimal" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_rate_per_day">Rate per Day</label>
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
                <button id="cancelAddPosition" type="button" class="btn btn-light-secondary"
                    data-bs-dismiss="modal">Cancel</button>
                <button id="submitAddPosition" type="submit" form="addPositionForm" class="btn btn-primary">Create
                    Position</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="proofOfPaymentModal" tabindex="-1" aria-labelledby="proofOfPaymentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proofOfPaymentModalLabel">Confirm Receipt of Salary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    By printing this payslip, you are declaring that you have <strong>received your salary</strong> for
                    this period.
                </div>
                <p class="mb-3">Please upload a photo/screenshot as proof of payment (e.g., bank transfer receipt, cash
                    acknowledgement) to proceed.</p>

                <form id="proofOfPaymentForm" enctype="multipart/form-data">
                    <input type="hidden" id="proof_payroll_id" name="payroll_id">
                    <div class="mb-3">
                        <label for="proof_file" class="form-label fw-bold">Upload Proof (Image)</label>
                        <input class="form-control" type="file" id="proof_file" name="proof" accept="image/*" required>
                        <div class="form-text text-muted">Allowed formats: JPG, PNG, JPEG.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-submit-proof">
                    <i class="fa-solid fa-check me-1"></i> Confirm & Print
                </button>
            </div>
        </div>
    </div>
</div>
