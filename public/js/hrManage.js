$(document).ready(function () {
    $("#birth_date").on("change", function () {
        let birthday = new Date($(this).val());
        if (!isNaN(birthday.getTime())) {
            let ageDifMs = Date.now() - birthday.getTime();
            let ageDate = new Date(ageDifMs);
            $("#age").val(Math.abs(ageDate.getUTCFullYear() - 1970));
        }
    });

    $('input[type="tel"]').on("input", function () {
        $(this).val(
            $(this)
                .val()
                .replace(/[^\d+]/g, "")
        );
    });
});
var mode = "";
$(document).ready(function () {
    mode = $("#insert-btn-employee").data("mode");
    if (!mode) {
        console.warn("insert-btn-employee not found");
        return;
    }
    setButtonMode(mode);

    function setButtonMode(mode) {
        const button = document.getElementById("insert-btn-employee");
        if (!button) return;
        // console.log("Mode:", mode);
        if (mode === "add") {
            $("#insert-btn-employee").removeAttr("hidden");
            $("#edit-btn-employee").attr("hidden", true);
            $("#email").removeAttr("readonly");
        } else if (mode === "edit") {
            $("#insert-btn-employee").attr("hidden", true);
            $("#edit-btn-employee").removeAttr("hidden");
            $("#email").attr("readonly", "readonly");
        }
    }
});

$(document).ready(function () {
    const departmentSelect = document.getElementById("department");
    const positionSelect = document.getElementById("position");
    const supervisorSelect = document.getElementById("supervisor");
    const baseSalary = [20800.0, 16640.0, 15600.0, 14560.0, 13520.0];

    if (mode === "add") {
        departmentSelect.addEventListener("change", updatePositions);
        positionSelect.addEventListener("change", updateSupervisors);
    } else if (mode === "edit") {
        const value = $("#supervisor").data("value").split("|");
        const raw = $("#position").data("value");
        const [position, positionId, levelValue] = raw.split("|");

        console.log(levelValue);
        supervisorSelect.innerHTML;
        const option = document.createElement("option");
        option.value = value[1];
        option.textContent = value[0];
        supervisorSelect.appendChild(option).selected = true;

        positionSelect.innerHTML;
        const opt = document.createElement("option");
        opt.value = positionId;
        option.dataset.level = levelValue;
        opt.textContent = position;
        positionSelect.appendChild(opt).selected = true;

        $("#level").val(levelValue.toUpperCase());

        departmentSelect.addEventListener("change", updatePositions);
        positionSelect.addEventListener("change", updateSupervisors);
    }
    function updatePositions() {
        $("#level").val("");
        $("#base_salary").val("");
        const department = departmentSelect.value;
        if (department) {
            fetch(
                `/positions-by-department?department=${encodeURIComponent(
                    department
                )}`
            )
                .then((res) => res.json())
                .then((data) => {
                    const positionSelect = document.getElementById("position");
                    positionSelect.innerHTML =
                        '<option value="" disabled selected>Choose...</option>';

                    data.forEach((p) => {
                        const option = document.createElement("option");
                        option.dataset.level = p.level;
                        option.value = p.id;
                        option.textContent = p.name;
                        positionSelect.appendChild(option);
                    });
                });
        }
    }
    function updateSupervisors() {
        // console.log('Department or Position changed');
        const department = departmentSelect.value;
        const position = positionSelect.value;
        const level = $("#position option:selected").data("level");
        $("#level").val(level.toUpperCase());
        updateSalary();
        if (position && department) {
            fetch(
                `/supervisors-by-department-and-position?department=${encodeURIComponent(
                    department
                )}&position=${encodeURIComponent(position)}`
            )
                .then((response) => response.json())
                .then((data) => {
                    supervisorSelect.innerHTML =
                        '<option value="" disabled selected>Choose...</option>';
                    data.forEach((s) => {
                        const option = document.createElement("option");
                        option.value = s.id;
                        option.textContent = s.first_name + " " + s.last_name;
                        supervisorSelect.appendChild(option);
                    });
                });
        } else {
            supervisorSelect.innerHTML =
                '<option value="" disabled selected>Choose...</option>';
        }
    }
    function updateSalary() {
        const position = $("#position option:selected").val();
        // console.log(typeof(position));
        switch (position) {
            case "1":
                $("#base_salary").val(baseSalary[0]);
                return;
            case "2":
                $("#base_salary").val(baseSalary[0]);
                return;
            case "3":
                $("#base_salary").val(baseSalary[1]);
                return;
            case "4":
                $("#base_salary").val(baseSalary[0]);
                return;
            case "5":
                $("#base_salary").val(baseSalary[0]);
                return;
            case "6":
                $("#base_salary").val(baseSalary[0]);
                return;
            case "7":
                $("#base_salary").val(baseSalary[1]);
                return;
            case "8":
                $("#base_salary").val(baseSalary[0]);
                return;
            case "9":
                $("#base_salary").val(baseSalary[2]);
                return;
            case "10":
                $("#base_salary").val(baseSalary[4]);
                return;
            case "11":
                $("#base_salary").val(baseSalary[0]);
                return;
            case "12":
                $("#base_salary").val(baseSalary[1]);
                return;
            case "13":
                $("#base_salary").val(baseSalary[4]);
                return;
            case "14":
                $("#base_salary").val(baseSalary[3]);
                return;
            case "15":
                $("#base_salary").val(baseSalary[4]);
                return;
        }
    }
});

$("#reset").click(function (e) {
    $("#employeeForm")[0].reset();
});

$("#insert-btn-employee").click(function (e) {
    e.preventDefault();
    // console.log('Submit button clicked');
    let form = document.getElementById("employeeForm");
    let formData = new FormData(form);
    $("#LoadingScreen").fadeIn(200);

    $.ajax({
        url: "/humanresources/store-employee",
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $("#LoadingScreen").fadeOut(200);
            Toast.fire({
                text: response.message,
                icon: "success",
            }).then(() => location.reload());
        },
        error: function (xhr) {
            // console.error('Error response:', xhr);
            $("#LoadingScreen").fadeOut(200);
            if (xhr.responseJSON?.errors) {
                let errorMessages = Object.values(xhr.responseJSON.errors)
                    .flat()
                    .join("\n");
                Swal.fire("Validation Error", errorMessages, "error");
            } else {
                Swal.fire("Error", "An unexpected error occurred.", "error");
            }
        },
    });
});

$("#edit-btn-employee").click(function (e) {
    e.preventDefault();
    // console.log('Submit button clicked');
    let form = document.getElementById("employeeForm");
    let employee_id = $("#email").data("value");
    // console.log("Employee ID:", employee_id);
    let formData = new FormData($("#employeeForm")[0]);
    formData.append("_method", "PUT");
    // let updateUrl = "{{ route('humanresources/update-employee') }}/" + employee_id;
    let url = `/humanresources/update-employee/${employee_id}`;
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to update this employee's information.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, update it!",
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }
        $("#LoadingScreen").fadeIn(200);
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            // url: "humanresources/update-employee/" + employee_id,
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $("#LoadingScreen").fadeOut(200);
                Toast.fire({
                    text: response.message,
                    icon: "success",
                }).then(() => location.reload());
            },
            error: function (xhr) {
                // console.error('Error response:', xhr);
                $("#LoadingScreen").fadeOut(200);
                if (xhr.responseJSON?.errors) {
                    let errorMessages = Object.values(xhr.responseJSON.errors)
                        .flat()
                        .join("\n");
                    Toast.fire("Validation Error", errorMessages, "error");
                } else {
                    Toast.fire(
                        "Error",
                        "An unexpected error occurred.",
                        "error"
                    );
                }
            },
        });
    });
});
