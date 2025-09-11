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
document.addEventListener("DOMContentLoaded", function () {
    const departmentSelect = document.getElementById("department");
    const positionSelect = document.getElementById("position");
    const supervisorSelect = document.getElementById("direct_supervisor");

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

    departmentSelect.addEventListener("change", updateSupervisors);
    positionSelect.addEventListener("change", updateSupervisors);
});

$("#reset").click(function (e) {
    $("#employeeForm")[0].reset();
});

$("#submit-btn-employee").click(function (e) {
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
            });
            $("#employeeForm")[0].reset();
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

