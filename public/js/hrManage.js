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
    $("#postal_code").on("input", function () {
        value = value.replace(/[^\d]/g, '');
        if (value.length > 4) {
            value = value.slice(0, 4);
        }
        $(this).val(value);
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
            $("#cancel").attr("hidden", true);
            $("#email").removeAttr("readonly");
        } else if (mode === "edit") {
            $("#insert-btn-employee").attr("hidden", true);
            $("#reset").attr("hidden", true);
            $("#edit-btn-employee").removeAttr("hidden");
            $("#cancel").removeAttr("hidden");
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
                `/human-resources/positions-by-department?department=${encodeURIComponent(
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
        const department = departmentSelect.value;
        const position = positionSelect.value;
        const level = $("#position option:selected").data("level");
        $("#level").val(level.toUpperCase());
        updateSalary();
        if (position && department) {
            fetch(
                `/human-resources/supervisors-by-department-and-position?department=${encodeURIComponent(
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
                $("#base_salary").val(baseSalary[4]);
                return;
            case "8":
                $("#base_salary").val(baseSalary[0]);
                return;
            case "9":
                $("#base_salary").val(baseSalary[1]);
                return;
            case "10":
                $("#base_salary").val(baseSalary[0]);
                return;
            case "11":
                $("#base_salary").val(baseSalary[2]);
                return;
            case "12":
                $("#base_salary").val(baseSalary[4]);
                return;
            case "13":
                $("#base_salary").val(baseSalary[0]);
                return;
            case "14":
                $("#base_salary").val(baseSalary[1]);
                return;
            case "15":
                $("#base_salary").val(baseSalary[4]);
                return;
            case "16":
                $("#base_salary").val(baseSalary[3]);
                return;
            case "17":
                $("#base_salary").val(baseSalary[4]);
                return;
        }
    }
});

$("#reset").click(function (e) {
    e.preventDefault();
    const $form = $("#employeeForm");
    $form.find("select").val("").trigger("change");
    $form.find('input[type="date"]').val("");
    $form.find("input, select").val("");
    $form.find(".is-invalid").removeClass("is-invalid");
    $form.find(".invalid-feedback").remove();
    $("#sss").val(600.0);
    $("#philhealth").val(450.0);
    $("#pagibig").val(100.0);
});
$("#cancel").click(function (e) {
    e.preventDefault();
    window.history.back();
});

$("#insert-btn-employee").click(function (e) {
    e.preventDefault();
    let isValid = true;
    const $form = $("#employeeForm");

    $form.find("input, select, option").each(function () {
        const $field = $(this);
        const value = $field.val();
        if ($field.prop("required") && (!value || !value.trim())) {
            $field.addClass("is-invalid");
            isValid = false;
        } else {
            $field.removeClass("is-invalid");
        }
    });

    if (isValid) {
        let form = document.getElementById("employeeForm");
        let formData = new FormData(form);
        $("#LoadingScreen").fadeIn(200);

        $.ajax({
            url: "/human-resources/store-employee",
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
                $("#LoadingScreen").fadeOut(200);
                if (xhr.responseJSON?.errors) {
                    let errorMessages = Object.values(xhr.responseJSON.errors)
                        .flat()
                        .join("\n");
                    Swal.fire("Validation Error", errorMessages, "error");
                } else {
                    Swal.fire(
                        "Error",
                        "An unexpected error occurred.",
                        "error"
                    );
                }
            },
        });
    }
});

$("#edit-btn-employee").click(function (e) {
    e.preventDefault();

    let form = document.getElementById("employeeForm");
    let employee_id = $("#email").data("value");
    let formData = new FormData($("#employeeForm")[0]);
    formData.append("_method", "PUT");

    let url = `/human-resources/update-employee/${employee_id}`;
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
