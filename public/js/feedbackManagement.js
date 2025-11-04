$(document).ready(function () {

    function escapeAttribute(s) {
        if (!s) return '';
        return s.toString()
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    const feedbackTable = $('#feedbackTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/customer-service/feedbacks",
            type: "GET",
            dataSrc: "data",
            error: function (xhr, error, thrown) {
                console.error("Failed to load feedback data for DataTable.", error);
            }
        },
        columns: [
            { data: "name", width: "15%" },
            {
                data: "overall_rating",
                className: "text-center",
                width: "8%",
                render: function (data, type, row) {
                    return `<span class="card-rating">${data} ★</span>`;
                }
            },
            {
                data: "message",
                width: "25%",
                render: function (data, type, row) {
                    const fullMessage = data || "No comments provided.";
                    const maxLength = 100;
                    if (fullMessage.length > maxLength) {
                        const truncatedMessage = fullMessage.substring(0, maxLength) + "...";
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
                }
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
                }
            },
            {
                data: "created_at",
                width: "18%",
                render: function (data, type, row) {
                    const submissionDate = new Date(data).toLocaleString("en-US", {
                        year: "numeric", month: "long", day: "numeric",
                        hour: "2-digit", minute: "2-digit",
                    });
                    return `
                        <small>
                            ${submissionDate}
                            <br>
                            <strong>Location:</strong> ${row.location || "N/A"}
                        </small>
                    `;
                }
            },
            {
                data: "status",
                className: "text-center",
                width: "8%",
                render: function (data, type, row) {
                    return data === 35 ? '<span class="badge bg-success">Displayed</span>' : '<span class="badge bg-danger">Hidden</span>';
                }
            },
            {
                data: "id",
                orderable: false,
                className: "text-center",
                width: "8%",
                render: function (data, type, row) {
                    if (row.status === 35) {
                        return `<button class="btn btn-warning btn-sm hide-feedback-btn" data-id="${data}">Hide</button>`;
                    } else {
                        return `<button class="btn btn-success btn-sm display-feedback-btn" data-id="${data}">Display</button>`;
                    }
                }
            }
        ],
        createdRow: function (row, data, dataIndex) {
            if (data.status === 34) {
                $(row).addClass('feedback-hidden');
            } else {
                $(row).addClass('feedback-displayed');
            }
        },
        order: [[4, 'desc']]
    });

    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            let filterDate = $('#submitted_date_filter').val();
            if (!filterDate) {
                return true;
            }

            let rowData = feedbackTable.row(dataIndex).data();
            let rowDate = rowData.created_at.substring(0, 10);

            if (filterDate === rowDate) {
                return true;
            }
            return false;
        }
    );

    $('#status_filter').on('change', function () {
        const selectedStatus = $(this).val();
        feedbackTable.column(5).search(selectedStatus).draw();
    });

    $('#submitted_date_filter').on('change', function () {
        feedbackTable.draw();
    });

    const tableBody = '#feedbackTable tbody';

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
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const originalText = $button.text();

        $.ajax({
            url: `/customer-service/feedbacks/update-status/${feedbackId}`,
            method: "POST",
            data: {
                status: newStatus,
                _token: csrfToken
            },
            beforeSend: function () {
                $button.prop("disabled", true).text(newStatus === 34 ? "Hiding..." : "Displaying...");
            },
            success: function (response) {
                feedbackTable.ajax.reload(null, false);
            },
            error: function (xhr) {
                console.error("Failed to update status.", xhr.responseText);
                $button.text("Error!");
                setTimeout(() => {
                    $button.prop("disabled", false).text(originalText);
                }, 2000);
            }
        });
    }
});
