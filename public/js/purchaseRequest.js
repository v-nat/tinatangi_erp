$(document).ready(function () {
    $("#purchaseReqTable").DataTable({
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/finance/purchases/get-requests",
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
                width: "50px",
            },
            { data: "type" },
            { data: "department" },
            {
                data: "amount",
                render: function (data, type, row) {
                    return (
                        "₱ " +
                        parseFloat(data).toLocaleString("en-PH", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        })
                    );
                },
            },
            { data: "requested_by_id" },
            { data: "requested_date" },
            { data: "remarks" },
            {
                data: "status",
                className: "text-center",
            },
            {
                data: "id",
                render: function (data, type, row) {
                    if (
                        row.status !==
                        '<span class="badge bg-warning">Pending</span>'
                    ) {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}"
                            title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </div>
                        `;
                    } else {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}"
                            title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-primary bs-tooltip me-2 process-btn"
                                data-id="${data}"
                                title="Process">
                                    <i class="fa-solid fa-check"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-danger bs-tooltip me-2 reject-btn"
                                data-id="${data}"
                                title="Reject">
                                    <i class="fa-solid fa-x"></i>
                            </a>
                        </div>
                        `;
                    }
                },
                className: "text-center",
            },
        ],
    });

    $(document).on("click", ".process-btn", function () {
        const req_id = $(this).data("id");

        Swal.fire({
            title: "Process Purchase Request?",
            text: "You are about to put on process this purchase order request.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm!",
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            $("#LoadingScreen").fadeIn(200);
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                url: `/finance/purchases/process/${req_id}/14`,
                type: "PUT",
                data: null,
                processData: false,
                contentType: false,
                success: function (response) {
                    $("#LoadingScreen").fadeOut(200);
                    reloadTable("purchaseReqTable");
                    Toast.fire({
                        text: response.message,
                        icon: "success",
                    });
                },
                error: function (xhr) {
                    // console.error('Error response:', xhr);
                    $("#LoadingScreen").fadeOut(200);
                    if (xhr.responseJSON?.errors) {
                        let errorMessages = Object.values(
                            xhr.responseJSON.errors
                        )
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

    $(document).on("click", ".reject-btn", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        $("#rejectionRequestId").val(id);
        $("#RejectionConfirmation").modal("show");
    });

    $("#reject-btn-confirmed").click(function (e) {
        e.preventDefault();
        let id = $("#rejectionRequestId").val();
        let remarks = $("#rejectionNotes").val();
        if (remarks) {
            $("#LoadingScreen").fadeIn(200);
            $("#rejectionModal").modal("hide");
            $.ajax({
                url: `/finance/purchases/process/${id}/12`,
                method: "PUT",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    id: id,
                    remarks: remarks,
                },
                success: function (response) {
                    if (response.success) {
                        $("#LoadingScreen").fadeOut(200);
                        reloadTable('purchaseReqTable');
                        Toast.fire(
                            "Rejected!",
                            response.message,
                            "success"
                        );
                    } else {
                        Toast.fire("Error", response.message, "error");
                    }
                },
                error: function (xhr) {
                    Toast.fire(
                        "Error",
                        xhr.responseJSON?.message || "Something went wrong",
                        "error"
                    );
                },
            });
        } else {
            Toast.fire({
                icon: "error",
                title: "Error",
                text: "Please provide a remarks",
                timer: 1500,
            });
        }
    });

    function reloadTable(tableId) {
        $("#" + tableId).DataTable().ajax.reload(null, false);
    }
});
