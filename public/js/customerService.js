$(document).ready(function () {
    /**
     * Helper function to safely escape text for a data attribute.
     * @param {string} s - The string to escape.
     * @returns {string} The escaped string.
     */
    function escapeAttribute(s) {
        if (!s) return '';
        return s.toString()
                .replace(/"/g, '&quot;')  // Escape double quotes
                .replace(/'/g, '&#39;')  // Escape single quotes
                .replace(/</g, '&lt;')   // Escape less-than
                .replace(/>/g, '&gt;');  // Escape greater-than
    }

    /**
     * Loads and displays feedback, paginated.
     * @param {number} [page=1] - The page number to load.
     */
    function loadFeedbacks(page = 1) {
        const feedbackContainer = $("#feedback-container");

        $.ajax({
            url: `/customer-service/feedbacks?page=${page}`,
            method: "GET",
            beforeSend: function () {
                feedbackContainer.html("<p>Loading feedback...</p>");
            },
            success: function (response) {
                feedbackContainer.empty();

                if (!response.data || response.data.length === 0) {
                    feedbackContainer.html(
                        "<p>No feedback has been submitted yet.</p>"
                    );
                    return;
                }

                let feedbackCount = 0;

                response.data.forEach((feedback) => {

                    feedbackCount++;

                    const photoHtml = feedback.photo
                        ? `<div class="card-photo mt-2">
                             <a href="#" class="view-photo-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#imageModal"
                                data-image-url="/storage/app/${feedback.photo}">
                                View Uploaded Photo
                             </a>
                           </div>`
                        : "";

                    const submissionDate = new Date(
                        feedback.created_at
                    ).toLocaleString("en-US", {
                        year: "numeric",
                        month: "long",
                        day: "numeric",
                        hour: "2-digit",
                        minute: "2-digit",
                    });

                    // NEW: Logic for truncating message and adding "See More"
                    const fullMessage = feedback.message || "No comments provided.";
                    const maxLength = 100; // Truncate after 100 chars
                    let messageHtml = "";

                    if (fullMessage.length > maxLength) {
                        const truncatedMessage = fullMessage.substring(0, maxLength) + "...";
                        // We use escapeAttribute to safely store the full message
                        const encodedMessage = escapeAttribute(fullMessage);

                        messageHtml = `<p><em>"${truncatedMessage}"</em>
                                         <a href="#" class="see-more-btn ms-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#messageModal"
                                            data-full-message="${encodedMessage}">
                                            See More
                                         </a>
                                       </p>`;
                    } else {
                        messageHtml = `<p><em>"${fullMessage}"</em></p>`;
                    }


                    let buttonHtml = "";
                    let cardOpacityClass = "";

                    if (feedback.status === 35) {
                        buttonHtml = `<button class="btn btn-warning btn-sm hide-feedback-btn" data-id="${feedback.id}">
                                          Hide
                                      </button>`;
                        cardOpacityClass = "feedback-displayed";
                    } else if (feedback.status === 34) {
                        buttonHtml = `<button class="btn btn-success btn-sm display-feedback-btn" data-id="${feedback.id}">
                                          Display
                                      </button>`;
                        cardOpacityClass = "feedback-hidden";
                    }

                    // NEW: Added h-100 to feedback-card for uniform height
                    const cardHtml = `
                        <div class="col-md-6 col-lg-4 ${cardOpacityClass}" data-feedback-id="${feedback.id}">
                            <div class="feedback-card h-100">
                                <div class="card-header">
                                    <h5>${feedback.name}</h5>
                                    <span class="card-rating">${
                                        feedback.overall_rating
                                    } ★</span>
                                </div>
                                <div class="card-body">
                                    ${messageHtml}

                                    <div class="rating-details mt-3">
                                        <p class="mb-1"><strong>Environment:</strong> ${
                                            feedback.environment_rating
                                        } ★</p>
                                        <p class="mb-1"><strong>Food:</strong> ${
                                            feedback.food_rating
                                        } ★</p>
                                        <p class="mb-1"><strong>Staff:</strong> ${
                                            feedback.staff_rating
                                        } ★</p>
                                    </div>
                                    ${photoHtml}
                                </div>
                                <div class="card-footer">
                                    <strong>Location:</strong> ${
                                        feedback.location || "N/A"
                                    }<br>
                                    <strong>IP:</strong> ${feedback.ip_address || "N/A"}<br>
                                    <small>Submitted on: ${submissionDate}</small>

                                    <!-- NEW: Button container now renders dynamically -->
                                    <div class="mt-2 text-end" data-btn-container="1">
                                        ${buttonHtml}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    feedbackContainer.append(cardHtml);
                });

                if (feedbackCount === 0) {
                     feedbackContainer.html(
                        "<p>No feedback to display.</p>"
                    );
                }

            },
            error: function () {
                feedbackContainer.html(
                    "<p class='text-danger'>Failed to load feedback. Please try again later.</p>"
                );
            },
        });
    }

    loadFeedbacks(1);

    $(document).on("click", ".view-photo-btn", function (e) {
        e.preventDefault();
        const imageUrl = $(this).data("image-url");
        $("#modalImage").attr("src", imageUrl);
    });

    // NEW: Click handler for the "See More" button
    $(document).on("click", ".see-more-btn", function (e) {
        e.preventDefault();
        // Get the full message from the data attribute
        const fullMessage = $(this).data("full-message");
        // Set the text of the modal's body. Using .text() is safe.
        $("#modalMessageBody").text(fullMessage);
    });


    $(document).on("click", ".hide-feedback-btn", function (e) {
        e.preventDefault();

        const $button = $(this);
        const feedbackId = $button.data("id");
        const newStatus = 34;

        const $cardContainer = $button.closest(".col-md-6.col-lg-4");
        const $buttonContainer = $button.closest("[data-btn-container='1']");

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: `/customer-service/feedbacks/update-status/${feedbackId}`,
            method: "POST",
            data: {
                status: newStatus,
                _token: csrfToken
            },
            beforeSend: function () {
                $button.prop("disabled", true).text("Hiding...");
            },
            success: function (response) {

                const displayButtonHtml = `<button class="btn btn-success btn-sm display-feedback-btn" data-id="${feedbackId}">
                                               Display
                                           </button>`;
                $buttonContainer.html(displayButtonHtml);

                $cardContainer.removeClass("feedback-displayed").addClass("feedback-hidden");

            },
            error: function (xhr) {
                console.error("Failed to update status.", xhr.responseText);
                $button.text("Error!");
                setTimeout(() => {
                     $button.prop("disabled", false).text("Hide");
                }, 2000);
            }
        });
    });

    $(document).on("click", ".display-feedback-btn", function (e) {
        e.preventDefault();

        const $button = $(this);
        const feedbackId = $button.data("id");
        const newStatus = 35;

        const $cardContainer = $button.closest(".col-md-6.col-lg-4");
        const $buttonContainer = $button.closest("[data-btn-container='1']");

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: `/customer-service/feedbacks/update-status/${feedbackId}`,
            method: "POST",
            data: {
                status: newStatus,
                _token: csrfToken
            },
            beforeSend: function () {
                $button.prop("disabled", true).text("Displaying...");
            },
            success: function (response) {
                const hideButtonHtml = `<button class="btn btn-warning btn-sm hide-feedback-btn" data-id="${feedbackId}">
                                            Hide
                                        </button>`;

                $buttonContainer.html(hideButtonHtml);

                $cardContainer.removeClass("feedback-hidden").addClass("feedback-displayed");

            },
            error: function (xhr) {
                console.error("Failed to update status.", xhr.responseText);
                $button.text("Error!");
                setTimeout(() => {
                     $button.prop("disabled", false).text("Display");
                }, 2000);
            }
        });
    });

});

