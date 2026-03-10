import $ from 'jquery';
import Swal from 'sweetalert2';

$(document).ready(function () {
    function escapeAttribute(s) {
        if (!s) return "";
        return s
            .toString()
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
    }

    const feedbackTable = $("#feedbackTable").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/customer-service/feedbacks",
            type: "GET",
            dataSrc: "data",
            error: function (xhr, error, thrown) {
                console.error(
                    "Failed to load feedback data for DataTable.",
                    error
                );
            },
        },
        columns: [
            {
                data: null,
                orderable: false,
                className: "text-center select-checkbox",
                width: "1%",
                defaultContent:
                    '<input type="checkbox" class="form-check-input feedback-select-checkbox">',
                title: '<input type="checkbox" class="form-check-input" id="selectAllFeedback">',
            },
            { data: "name", width: "15%" },
            {
                data: "overall_rating",
                className: "text-center",
                width: "8%",
                render: function (data, type, row) {
                    return `<span class="card-rating">${data} ★</span>`;
                },
            },
            {
                data: "message",
                width: "25%",
                render: function (data, type, row) {
                    const fullMessage = data || "No comments provided.";
                    const maxLength = 100;
                    if (fullMessage.length > maxLength) {
                        const truncatedMessage =
                            fullMessage.substring(0, maxLength) + "...";
                        const encodedMessage = escapeAttribute(fullMessage);
                        return `<p><em>"${truncatedMessage}"</em>
                                  <a href="#" class="see-more-btn ms-1"
                                     data-bs-toggle="modal"
                                     data-bs-target="#messageModal"
                                     data-full-message="${encodedMessage}">
                                     See More
                                  </a>
                                </p>`;
                    }
                    return `<p><em>"${fullMessage}"</em></p>`;
                },
            },
            {
                data: null,
                orderable: false,
                width: "18%",
                render: function (data, type, row) {
                    const photoHtml = row.photo
                        ? `<div class="card-photo mt-2">
                             <a href="#" class="view-photo-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#imageModal"
                                data-image-url="/storage/app/public/${row.photo}">
                                View Uploaded Photo
                             </a>
                           </div>`
                        : "";

                    return `
                        <div class="rating-details">
                            <small><strong>Environment:</strong> ${row.environment_rating} ★</small>
                            <small><strong>Food:</strong> ${row.food_rating} ★</small>
                            <small><strong>Staff:</strong> ${row.staff_rating} ★</small>
                        </div>
                        ${photoHtml}
                    `;
                },
            },
            {
                data: "created_at",
                width: "18%",
                render: function (data, type, row) {
                    const submissionDate = new Date(data).toLocaleString(
                        "en-US",
                        {
                            year: "numeric",
                            month: "long",
                            day: "numeric",
                            hour: "2-digit",
                            minute: "2-digit",
                        }
                    );
                    return `
                        <small>
                            ${submissionDate}
                            <br>
                            <strong>Location:</strong> ${row.location || "N/A"}
                        </small>
                    `;
                },
            },
            {
                data: "status",
                className: "text-center",
                width: "8%",
                render: function (data, type, row) {
                    return data === 35
                        ? '<span class="badge bg-success">Displayed</span>'
                        : '<span class="badge bg-danger">Hidden</span>';
                },
            },
            {
                data: "id",
                orderable: false,
                className: "text-center",
                render: function (data, type, row) {
                    const hideButton = `<button class="btn btn-warning btn-sm hide-feedback-btn" title="Hide Feedback" data-id="${data}"><i class="fas fa-eye-slash"></i></button>`;
                    const displayButton = `<button class="btn btn-success btn-sm display-feedback-btn" title="Display Feedback" data-id="${data}"><i class="fas fa-eye"></i></button>`;

                    const statusButton = (row.status === 35) ? hideButton : displayButton;
                    const deleteButton = `<button class="btn btn-danger btn-sm delete-feedback-btn" title="Delete Feedback" data-id="${data}"><i class="fas fa-trash-alt"></i></button>`;

                    return `<div class="btn-group btn-group-sm" role="group">${statusButton}${deleteButton}</div>`;
                }
            },
        ],
        createdRow: function (row, data, dataIndex) {
            if (data.status === 34) {
                $(row).addClass("feedback-hidden");
            } else {
                $(row).addClass("feedback-displayed");
            }
        },
        order: [[5, "desc"]],
    });

    feedbackTable.on('draw.dt', function () {
        $('#feedbackTable [title]').tooltip({
            container: 'body'
        });
    });

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        let filterDate = $("#submitted_date_filter").val();
        if (!filterDate) {
            return true;
        }

        let rowData = feedbackTable.row(dataIndex).data();
        let rowDate = rowData.created_at.substring(0, 10);

        if (filterDate === rowDate) {
            return true;
        }
        return false;
    });

    $("#status_filter").on("change", function () {
        const selectedStatus = $(this).val();
        feedbackTable.column(6).search(selectedStatus).draw();
    });

    $("#submitted_date_filter").on("change", function () {
        feedbackTable.draw();
    });

    const tableBody = "#feedbackTable tbody";

    $(tableBody).on("click", ".view-photo-btn", function (e) {
        e.preventDefault();
        const imageUrl = $(this).data("image-url");
        $("#modalImage").attr("src", imageUrl);
    });

    $(tableBody).on("click", ".see-more-btn", function (e) {
        e.preventDefault();
        const fullMessage = $(this).data("full-message");
        $("#modalMessageBody").text(fullMessage);
    });

    $(tableBody).on("click", ".hide-feedback-btn", function (e) {
        e.preventDefault();
        const $button = $(this);
        const feedbackId = $button.data("id");
        updateFeedbackStatus($button, feedbackId, 34);
    });

    $(tableBody).on("click", ".display-feedback-btn", function (e) {
        e.preventDefault();
        const $button = $(this);
        const feedbackId = $button.data("id");
        updateFeedbackStatus($button, feedbackId, 35);
    });

    function updateFeedbackStatus($button, feedbackId, newStatus) {
        const csrfToken = $('meta[name="csrf-token"]').attr("content");
        const originalText = $button.text();

        $.ajax({
            url: `/customer-service/feedbacks/update-status/${feedbackId}`,
            method: "POST",
            data: {
                status: newStatus,
                _token: csrfToken,
            },
            beforeSend: function () {
                $button
                    .prop("disabled", true)
                    .text(newStatus === 34 ? "..." : "...");
            },
            success: function (response) {
                feedbackTable.ajax.reload(null, false);
                Toast.fire({
                    icon: "success",
                    title: "Success!",
                    text: response.message,
                    timer: 1500,
                });
            },
            error: function (xhr) {
                console.error("Failed to update status.", xhr.responseText);
                $button.text("Error!");
                setTimeout(() => {
                    $button.prop("disabled", false).text(originalText);
                }, 2000);
            },
        });
    }

    $(tableBody).on("click", ".delete-feedback-btn", function (e) {
        e.preventDefault();
        const $button = $(this);
        const feedbackId = $button.data("id");
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/customer-service/feedbacks/destroy/${feedbackId}`,
                    method: "DELETE",
                    data: {
                        _token: csrfToken,
                    },
                    beforeSend: function () {
                        $button.prop("disabled", true).text("Deleting...");
                        $("#LoadingScreen").fadeIn(200);
                    },
                    success: function (response) {
                        feedbackTable.ajax.reload(null, false);
                        $("#LoadingScreen").fadeOut(200);
                        Toast.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: response.message,
                            timer: 1500,
                        });
                    },
                    error: function (xhr) {
                        $("#LoadingScreen").fadeOut(200);
                        $button.text("Error!");
                        setTimeout(() => {
                            $button.prop("disabled", false).text("Delete");
                        }, 2000);

                        Swal.fire(
                            "Error!",
                            "Failed to delete the feedback. Please try again.",
                            "error"
                        );
                    },
                });
            }
        });
    });

    $(document).on("change", "#selectAllFeedback", function () {
        const isChecked = $(this).prop("checked");

        $("#feedbackTable tbody input.feedback-select-checkbox").prop(
            "checked",
            isChecked
        );

        updateBatchDeleteButtonState();
    });

    $("#feedbackTable tbody").on(
        "change",
        "input.feedback-select-checkbox",
        function () {
            const totalCheckboxes = $(
                "#feedbackTable tbody input.feedback-select-checkbox"
            ).length;
            const checkedCheckboxes = $(
                "#feedbackTable tbody input.feedback-select-checkbox:checked"
            ).length;

            $("#selectAllFeedback").prop(
                "checked",
                totalCheckboxes === checkedCheckboxes
            );

            updateBatchDeleteButtonState();
        }
    );

    function updateBatchDeleteButtonState() {
        const selectedCount = getSelectedFeedbackIds().length;
        const $batchBtn = $("#batchDeleteBtn");

        if (selectedCount > 0) {
            $batchBtn
                .prop("disabled", false)
                .show()
                .text(`Batch Delete (${selectedCount})`);
        } else {
            $batchBtn
                .prop("disabled", true)
                .hide()
                .text("Batch Delete Selected");
        }
    }

    feedbackTable.on("draw.dt", function () {
        $("#selectAllFeedback").prop("checked", false);
        updateBatchDeleteButtonState();
    });

    function getSelectedFeedbackIds() {
        const selectedIds = [];
        $("#feedbackTable tbody input.feedback-select-checkbox:checked").each(
            function () {
                const row = $(this).closest("tr");
                const rowData = feedbackTable.row(row).data();
                selectedIds.push(rowData.id);
            }
        );
        return selectedIds;
    }

    $(document).on("click", "#batchDeleteBtn", function (e) {
        e.preventDefault();
        const selectedIds = getSelectedFeedbackIds();
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        if (selectedIds.length === 0) {
            Toast.fire({
                icon: "warning",
                title: "No Selection",
                text: "Please select at least one feedback item to delete.",
                timer: 2000,
            });
            return;
        }

        Swal.fire({
            title: "Confirm Batch Delete?",
            text: `You are about to delete ${selectedIds.length} feedback record(s). This cannot be undone.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, Delete All!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/customer-service/feedbacks/batch-destroy`,
                    method: "DELETE",
                    data: {
                        ids: selectedIds,
                        _token: csrfToken,
                    },
                    beforeSend: function () {
                        $("LoadingScreen").fadeIn(200);
                        $("#batchDeleteBtn")
                            .prop("disabled", true)
                            .text("Deleting...");
                    },
                    success: function (response) {
                        feedbackTable.ajax.reload(null, false);
                        $("#LoadingScreen").fadeOut(200);
                        Toast.fire({
                            icon: "success",
                            title: "Batch Deleted!",
                            text: response.message,
                            timer: 1500,
                        });
                    },
                    error: function (xhr) {
                        $("#LoadingScreen").fadeOut(200);
                        const errorMsg = xhr.responseJSON
                            ? xhr.responseJSON.message
                            : "An unknown error occurred.";

                        Swal.fire("Error!", errorMsg, "error");
                    },
                    complete: function () {
                        $("#selectAllFeedback").prop("checked", false);
                        $("#batchDeleteBtn")
                            .prop("disabled", false)
                            .text("Batch Delete Selected");
                    },
                });
            }
        });
    });

    $("#btn-refresh-feedback").on("click", function () {
        feedbackTable.ajax.reload(null, false);
    });
});
