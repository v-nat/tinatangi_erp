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
    const supervisorSelect = document.getElementById("direct_supervisor");
    if (mode === "add") {
        departmentSelect.addEventListener("change", updateSupervisors);
        positionSelect.addEventListener("change", updateSupervisors);
    } else if (mode === "edit") {
        const value = $("#direct_supervisor").data("value").split("|");
        // console.log('Direct Supervisor Value:', value);
        supervisorSelect.innerHTML;
        // '<option value="' +value[1] +'" disabled selected>' +value[0] +"</option>";
        const option = document.createElement("option");
        option.value = value[1];
        option.textContent = value[0];
        supervisorSelect.appendChild(option).selected = true;
        departmentSelect.addEventListener("change", updateSupervisors);
        positionSelect.addEventListener("change", updateSupervisors);
    }
    function updateSupervisors() {
        // console.log('Department or Position changed');
        const department = departmentSelect.value;
        const position = positionSelect.value;

        if (position.toLowerCase() !== "supervisor" && department) {
            fetch(
                `/supervisors-by-department?department=${encodeURIComponent(
                    department
                )}`
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
        } else if (position.toLowerCase() === "supervisor") {
            fetch(`/ceo`)
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
            Swal.fire({
                title: "Success!",
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
                Swal.fire({
                    title: "Success!",
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
                    Swal.fire(
                        "Error",
                        "An unexpected error occurred.",
                        "error"
                    );
                }
            },
        });
    });
});
