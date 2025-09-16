$(document).ready(function () {
    $("#employee_table").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/humanresources/employees/get",
            type: "GET",
            dataSrc: "data",
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                className: "text-center",
            },
            { data: "name" },
            { data: "position" },
            { data: "department" },
            { data: "email" },
            { data: "direct_supervisor" },
            {
                data: "status",
                render: function (data) {
                    return `<span class="badge bg-light-success ${
                        data === "active" ? "success" : "danger"
                    }">${data}</span>`;
                },
            },
            {
                data: "employee_id",
                render: function (data, type, row) {
                    return `
                    <div>
                        <a href="#" class="action-btn btn-edit bs-tooltip me-2"
                           data-id="${data}"
                           data-name="${row.name}"
                           data-employee-id="${row.employee_id}"
                           title="Edit">
                            <i class="fa-solid fa-user-pen"></i>
                        </a>
                        <a href="#" class="action-btn generate-payroll bs-tooltip"
                           title="Generate Payroll"
                           data-id="${data}"
                           data-name="${row.name}">
                            <i class="fa-solid fa-file-invoice"></i>
                        </a>
                    </div>
                        `;
                },
            },
        ],
    });
    function formatDate(date) {
        const d = new Date(date);
        let month = "" + (d.getMonth() + 1);
        let day = "" + d.getDate();
        const year = d.getFullYear();

        if (month.length < 2) month = "0" + month;
        if (day.length < 2) day = "0" + day;

        return [year, month, day].join("-");
    }

    $(document).on("click", ".btn-edit", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        window.location.href = "/humanresources/edit-employee/" + id;
    });
    $(document).on("click", ".generate-payroll", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        const name = $(this).data("name");
        $("#empId").text(id);
        $("#empName").text(name);
        $("#modalEmployeeId").val(id);

        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        $("#start_date").val(formatDate(firstDay));
        $("#end_date").val(formatDate(lastDay));
        // $("#generatedPayroll").removeAttr("aria-hidden");

        $("#generatePayroll").modal("show");
    });

    $('#payrollForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const formData = form.serialize();
        const submitBtn = form.find('button[type="submit"]');
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        Swal.fire({
            title: "Confirm Payroll Generation",
            html: `<div class="text-left">
                    <p>You are about to generate payroll for the following period:</p>
                    <ul class="list-unstyled">
                        <li><strong>Start Date:</strong> ${startDate}</li>
                        <li><strong>End Date:</strong> ${endDate}</li>
                    </ul>
                    <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Please verify the dates before proceeding.</p>
                </div>`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: "Generate Payroll",
            cancelButtonText: "Cancel",
            width: '500px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable button to prevent duplicate submissions
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Close the modal
                            $('#generatePayrollModal').modal('hide');

                            // Open printable page in new tab
                            // window.open(response.redirect_url, '_blank');

                            // Show success message
                            Swal.fire({
                                title: 'Success!',
                                text: 'Payroll generated successfully',
                                icon: 'success',
                                timer: 300,
                                showConfirmButton: false
                            }).then(() => window.location.href = "/humanresources/payroll");
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to generate payslip';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    },
                    complete: function() {
                        // Re-enable button
                        submitBtn.prop('disabled', false).html('Generate Payslip');
                    }
                });
            }
        });
    });
});