$(document).ready(function () {

    // Helper function to escape HTML attributes (still needed for modals)
    function escapeAttribute(s) {
        if (!s) return '';
        return s.toString()
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    // Initialize the DataTable
    const feedbackTable = $('#feedbackTable').DataTable({
        processing: true,
        serverSide: false, // We will load all data client-side, like your original script
        ajax: {
            url: "/customer-service/feedbacks", // URL to fetch all feedback data
            type: "GET",
            dataSrc: "data", // The array of feedback objects is in the 'data' property
            error: function (xhr, error, thrown) {
                console.error("Failed to load feedback data for DataTable.", error);
                // You could show a more permanent error in the table
            }
        },
        columns: [
            // 0. Customer
            { data: "name" },

            // 1. Rating (Overall)
            {
                data: "overall_rating",
                className: "text-center",
                render: function (data, type, row) {
                    return `<span class="card-rating">${data} ★</span>`;
                }
            },

            // 2. Comment
            {
                data: "message",
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

            // 3. Details (Composite column)
            {
                data: null, // No single data source
                orderable: false,
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

            // 4. Submitted (Composite column)
            {
                data: "created_at",
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

            // 5. Status
            {
                data: "status",
                className: "text-center",
                render: function (data, type, row) {
                    return data === 35 ? 'Displayed' : 'Hidden';
                }
            },

            // 6. Actions
            {
                data: "id", // Use the ID for the button
                orderable: false,
                className: "text-center",
                render: function (data, type, row) {
                    if (row.status === 35) {
                        // Status is 'Displayed', show 'Hide' button
                        return `<button class="btn btn-warning btn-sm hide-feedback-btn" data-id="${data}">Hide</button>`;
                    } else {
                        // Status is 'Hidden', show 'Display' button
                        return `<button class="btn btn-success btn-sm display-feedback-btn" data-id="${data}">Display</button>`;
                    }
                }
            }
        ],
        // This function applies the opacity class to the row
        createdRow: function (row, data, dataIndex) {
            if (data.status === 34) { // 34 = Hidden
                $(row).addClass('feedback-hidden');
            } else {
                $(row).addClass('feedback-displayed');
            }
        },
        // Default sort by submitted date (column 4), descending (newest first)
        order: [[4, 'desc']]
    });

    // --- Event Listeners (Moved from document to table body for efficiency) ---

    const tableBody = '#feedbackTable tbody';

    // Modal listener for "View Photo"
    $(tableBody).on("click", ".view-photo-btn", function (e) {
        e.preventDefault();
        const imageUrl = $(this).data("image-url");
        $("#modalImage").attr("src", imageUrl);
    });

    // Modal listener for "See More"
    $(tableBody).on("click", ".see-more-btn", function (e) {
        e.preventDefault();
        const fullMessage = $(this).data("full-message");
        $("#modalMessageBody").text(fullMessage);
    });

    // Action button listeners
    $(tableBody).on("click", ".hide-feedback-btn", function (e) {
        e.preventDefault();
        const $button = $(this);
        const feedbackId = $button.data("id");
        updateFeedbackStatus($button, feedbackId, 34); // 34 = Hide
    });

    $(tableBody).on("click", ".display-feedback-btn", function (e) {
        e.preventDefault();
        const $button = $(this);
        const feedbackId = $button.data("id");
        updateFeedbackStatus($button, feedbackId, 35); // 35 = Display
    });

    // --- AJAX Update Function (Slightly Modified) ---

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
                // SUCCESS: Reload the DataTable to show the change
                // 'null' re-fetches the data, 'false' keeps the user on the same page
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
