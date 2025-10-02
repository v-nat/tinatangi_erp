$(document).ready(function () {
    $("#approvalTable").DataTable({
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/finance/budgets/requests",
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
                width: "45px",
            },
            { data: "type", className: "dt-left"  },
            {
                data: "amount", className: "dt-left" ,
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
            { data: "requested_by_id", className: "dt-left"  },
            { data: "requested_at", className: "dt-left"  },
            { data: "department", className: "dt-left"  },
            { data: "notes", className: "dt-left"  },
            {
                data: "status",
                className: "text-center", width: "150px",
            },
            {
                data: "id",
                render: function (data, type, row) {
                    if (
                        row.status !==
                        '<span class="badge bg-warning">Pending</span>'
                    ) {
                        return " ";
                    } else {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-primary bs-tooltip me-2 approve-btn"
                                data-id="${data}"
                                data-request-id="${row.request_id}"
                                title="Approve">
                                    <i class="fa-solid fa-check"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-danger bs-tooltip me-2 reject-btn"
                                data-id="${data}"
                                data-request-id="${row.request_id}"
                                title="Reject">
                                    <i class="fa-solid fa-x"></i>
                            </a>
                        </div>
                        `;
                    }
                },
                className: "text-center", width: "150px",
            },
        ],
    });

    $(document).on("click", ".reject-btn", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        const request_id = $(this).data("request-id");
        $("#rejectionReleaseId").val(id);
        $("#rejectionRequestId").val(request_id);
        $("#RejectionConfirmation").modal("show");
    });

    $(document).on("click", ".approve-btn", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        const request_id = $(this).data("request-id");

        Swal.fire({
            title: "Approve Request?",
            text: "You are about to release a budget for this request.",
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
                url: `/finance/budgets/requests/approve/${id}/${request_id}`,
                type: "PUT",
                data: null,
                processData: false,
                contentType: false,
                success: function (response) {
                    $("#LoadingScreen").fadeOut(200);
                    reloadTable("approvalTable");
                    reloadTable("historyTable");
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

    $("#reject-btn-confirmed").click(function (e) {
        e.preventDefault();
        let id = $("#rejectionReleaseId").val();
        let notes = $("#rejectionNotes").val();
        let request_id = $("#rejectionRequestId").val();
        if (notes) {
            $("#LoadingScreen").fadeIn(200);
            $("#rejectionModal").modal("hide");
            $.ajax({
                url: `/finance/budgets/requests/reject/${id}`,
                method: "PUT",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    id: id,
                    notes: notes,
                    request_id: request_id,
                },
                success: function (response) {
                    if (response.success) {
                        $("#LoadingScreen").fadeOut(200);
                        reloadTable("approvalTable");
                        reloadTable("historyTable");
                        Toast.fire("Rejected!", response.message, "success");
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

    $("#historyTable").DataTable({
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/finance/budgets/history",
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
                width: "45px",
            },
            { data: "release_id", className: "dt-left"  },
            { data: "type", className: "dt-left"  },
            {
                data: "amount", className: "dt-left" ,
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
            { data: "requested_by_id", className: "dt-left"  },
            { data: "requested_at", className: "dt-left"  },
            { data: "department", className: "dt-left"  },
            { data: "released_by_id", className: "dt-left"  },
            { data: "released_at", className: "dt-left"  },
            {
                data: "status",
                className: "text-center", width: "150px",
            },
        ],
    });
    function reloadTable(tableId) {
        $("#" + tableId)
            .DataTable()
            .ajax.reload(null, false);
    }
});
