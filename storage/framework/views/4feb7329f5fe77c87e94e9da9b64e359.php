<?php echo $__env->make('partials.human-resources-heading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('emplMngt'); ?>active <?php $__env->stopSection(); ?>
<?php $__env->startSection('emplMngt2'); ?>active <?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi2'); ?>active <?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> <?php echo e($title); ?> Employee <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('hr.employees')); ?>">Employee Management</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo e($title); ?></li>
        </ol>
    </nav>
    <?php if(session('success')): ?>
        <div class="alert alert-success mb-4">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger mb-4">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <form id="employeeForm" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    
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
                                                name="first_name" value="<?php echo e(old('first_name', $data['first_name'] ?? '')); ?>" required>
                                            <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <div class="invalid-feedback">First Name is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="middle_name">Middle Name</label>
                                            <input type="text" id="middle_name" class="form-control" placeholder="Optional"
                                                name="middle_name" value="<?php echo e(old('middle_name', $data['middle_name'] ?? '')); ?>">
                                            <?php $__errorArgs = ['middle_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="last_name">Last Name *</label>
                                            <input type="text" id="last_name" class="form-control" placeholder="Enter Lastname"
                                                name="last_name" value="<?php echo e(old('last_name', $data['last_name'] ?? '')); ?>" required>
                                            <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <div class="invalid-feedback">Last Name is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="gender">Gender *</label>
                                            <select class="form-select" id="gender" name="gender" required>
                                                <option value="" disabled selected>Choose Gender</option>
                                                <option value="Male" <?php echo e(old('gender', $data['gender'] ?? '') == 'Male' ? 'selected' : ''); ?>>Male
                                                </option>
                                                <option value="Female" <?php echo e(old('gender', $data['gender'] ?? '') == 'Female' ? 'selected' : ''); ?>>
                                                    Female
                                                </option>
                                                <option value="Preferred not to say" <?php echo e(old('gender', $data['gender'] ?? '') == 'Preferred not to say' ? 'selected' : ''); ?>>
                                                    Preferred not to say
                                                </option>
                                            </select>
                                            <?php $__errorArgs = ['gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <div class="invalid-feedback">Gender is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="birth_date">Birthdate *</label>
                                            <input type="date" id="birth_date" class="form-control" name="birth_date"
                                                placeholder="1989-12-13" value="<?php echo e(old('birth_date', optional($data['birth_date'] ?? null)->format('Y-m-d') ?? '')); ?>" required>
                                            <?php $__errorArgs = ['birth_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <div class="invalid-feedback">Birthdate is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="age">Age</label>
                                            <input type="text" id="age" class="form-control" name="age" readonly
                                                placeholder="00" value="<?php echo e(old('age', $data['age'] ?? '')); ?>">
                                            <?php $__errorArgs = ['age'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="address">Address *</label>
                                            <input type="text" id="address" class="form-control" placeholder="Enter Address" required
                                                name="address" value="<?php echo e(old('address', $data['address'] ?? '')); ?>">
                                            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <div class="invalid-feedback">Address is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="postal_code">Postal Code *</label>
                                             <input type="tel" id="postal_code" class="form-control" name="postal_code" pattern="^\d{4}$" maxlength="4" required
                                                inputmode="numeric" placeholder="4114" value="<?php echo e(old('postal_code', $data['postal_code'] ?? '')); ?>">
                                            <?php $__errorArgs = ['postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <div class="invalid-feedback">Postal Code must be exactly 4 digits.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="email">Email *</label>
                                            <input type="email" id="email" class="form-control" name="email" data-value="<?php echo e($id ?? ''); ?>"
                                                placeholder="example@mail.com" value="<?php echo e(old('email', $data['email'] ?? '')); ?>" required>
                                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <div class="invalid-feedback">Email is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="phone_number">Phone Number *</label>
                                            <input type="tel" id="phone_number" class="form-control" name="phone_number"
                                                pattern="^(09|\+639)\d{9}$" maxlength="11" minlength="11" required
                                                placeholder="09123456789" value="<?php echo e(old('phone_number', $data['phone_number'] ?? '')); ?>">
                                            <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <div class="invalid-feedback">Phone Number must be a valid 11-digit PH number (09...).</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="citizenship">Citizenship *</label>
                                            <input type="text" id="citizenship" class="form-control" name="citizenship" required
                                                placeholder="Enter Citizenship" value="<?php echo e(old('citizenship', $data['citizenship'] ?? '')); ?>">
                                            <?php $__errorArgs = ['citizenship'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                                <?php if(isset($departments)): ?>
                                                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($department->id); ?>" <?php echo e(old('department', $data['department'] ?? '') == $department->id ? 'selected' : ''); ?>>
                                                            <?php echo e($department->name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </select>
                                            <?php $__errorArgs = ['department'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <div class="invalid-feedback">Department is required.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="supervisor">Direct Supervisor *</label>
                                            <select id="supervisor" name="supervisor_id" class="form-select" data-value="<?php echo e($data['supervisor'] ?? ''); ?>|<?php echo e($data['supervisor_id'] ?? ''); ?>" required>
                                                <option value=""  disabled selected>Choose Supervisor</option>
                                                
                                            </select>
                                            <?php $__errorArgs = ['supervisor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            <div class="invalid-feedback">Direct Supervisor is required.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="position">Position *</label>
                                            <select class="form-select" id="position" name="position_id" data-value="<?php echo e($data['position'] ?? ''); ?>|<?php echo e($data['position_id'] ?? ''); ?>|<?php echo e($data['level'] ?? ''); ?>" required>
                                                <option value="" disabled selected>Choose Position</option>
                                                
                                            </select>
                                            <?php $__errorArgs = ['position_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                    <input type="text" class="form-control" id="schedule_title" name="schedule_title" placeholder="e.g., Opening Shift" value="<?php echo e(old('schedule_title', optional($data['schedule'] ?? null)->title ?? '')); ?>">
                                    <div class="invalid-feedback" id="schedule_title_error"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Days of Week (Select exactly 6 days)</label>
                                    <div class="day-checkboxes">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" id="day_0" value="0" <?php if(in_array('0', old('days_of_week', (array) (optional($data['schedule'] ?? null)->days_of_week ?? [])))): echo 'checked'; endif; ?>>
                                            <label class="form-check-label" for="day_0">Sun</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" id="day_1" value="1" <?php if(in_array('1', old('days_of_week', (array) (optional($data['schedule'] ?? null)->days_of_week ?? [])))): echo 'checked'; endif; ?>>
                                            <label class="form-check-label" for="day_1">Mon</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" id="day_2" value="2" <?php if(in_array('2', old('days_of_week', (array) (optional($data['schedule'] ?? null)->days_of_week ?? [])))): echo 'checked'; endif; ?>>
                                            <label class="form-check-label" for="day_2">Tue</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" id="day_3" value="3" <?php if(in_array('3', old('days_of_week', (array) (optional($data['schedule'] ?? null)->days_of_week ?? [])))): echo 'checked'; endif; ?>>
                                            <label class="form-check-label" for="day_3">Wed</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" id="day_4" value="4" <?php if(in_array('4', old('days_of_week', (array) (optional($data['schedule'] ?? null)->days_of_week ?? [])))): echo 'checked'; endif; ?>>
                                            <label class="form-check-label" for="day_4">Thu</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" id="day_5" value="5" <?php if(in_array('5', old('days_of_week', (array) (optional($data['schedule'] ?? null)->days_of_week ?? [])))): echo 'checked'; endif; ?>>
                                            <label class="form-check-label" for="day_5">Fri</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="days_of_week[]" id="day_6" value="6" <?php if(in_array('6', old('days_of_week', (array) (optional($data['schedule'] ?? null)->days_of_week ?? [])))): echo 'checked'; endif; ?>>
                                            <label class="form-check-label" for="day_6">Sat</label>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" id="days_of_week_error">Please select exactly 6 days.</div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="schedule_time_in" class="form-label">Start Time</label>
                                        <input type="time" class="form-control" id="schedule_time_in" name="time_in" required value="<?php echo e(old('time_in', optional($data['schedule'] ?? null)->time_in ? Carbon\Carbon::parse(optional($data['schedule'] ?? null)->time_in)->format('H:i') : '')); ?>">
                                        <div class="invalid-feedback" id="time_in_error"></div>
                                    </div>
                                    <div class="col">
                                        <label for="schedule_time_out" class="form-label">End Time</label>
                                        <input type="time" class="form-control" id="schedule_time_out" name="time_out" required value="<?php echo e(old('time_out', optional($data['schedule'] ?? null)->time_out ? Carbon\Carbon::parse(optional($data['schedule'] ?? null)->time_out)->format('H:i') : '')); ?>">
                                        <div class="invalid-feedback" id="time_out_error"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="schedule_color" class="form-label">Color (Optional)</label>
                                    <input type="color" class="form-control form-control-color" id="schedule_color" name="color" value="<?php echo e(old('color', optional($data['schedule'] ?? null)->color ?? '#3788D8')); ?>" title="Choose a color">
                                     <div class="invalid-feedback" id="color_error"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="schedule_description" class="form-label">Description (Optional)</label>
                                    <textarea class="form-control" id="schedule_description" name="description" rows="2"><?php echo e(old('description', optional($data['schedule'] ?? null)->description ?? '')); ?></textarea>
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
                                                inputmode="decimal" name="base_salary" value="<?php echo e(old('base_salary', $data['base_salary'] ?? '')); ?>" readonly>
                                            <?php $__errorArgs = ['base_salary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="sss">SSS</label>
                                            <input type="text" id="sss" class="form-control" placeholder="₱0.00"
                                                inputmode="decimal" name="sss" value="<?php echo e(old('sss', $data['sss'] ?? '')); ?>" readonly>
                                            <?php $__errorArgs = ['sss'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="pagibig">Pag-ibig</label>
                                            <input type="text" id="pagibig" class="form-control" placeholder="₱0.00"
                                                inputmode="decimal" name="pagibig" value="<?php echo e(old('pagibig', $data['pagibig'] ?? '')); ?>" readonly>
                                            <?php $__errorArgs = ['pagibig'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="philhealth">Philhealth</label>
                                            <input type="text" id="philhealth" class="form-control" placeholder="₱0.00"
                                                inputmode="decimal" name="philhealth" value="<?php echo e(old('philhealth', $data['philhealth'] ?? '')); ?>" readonly>
                                            <?php $__errorArgs = ['philhealth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-3 mb-3 d-flex justify-content-start">
                        <button id="insert-btn-employee" data-mode="<?php echo e($mode ?? 'add'); ?>" class="btn btn-primary me-1 mb-1">Add Employee</button>
                        <button id="edit-btn-employee" hidden class="btn btn-primary me-1 mb-1">Update Employee</button>
                        <button type="reset" id="reset" class="btn btn-light-secondary me-1 mb-1">Reset</button>
                        <button type="button" id="cancel" hidden class="btn btn-light-secondary me-1 mb-1">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('js/hrManage.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/human_resources/manage-employee.blade.php ENDPATH**/ ?>