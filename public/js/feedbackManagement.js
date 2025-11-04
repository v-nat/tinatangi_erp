$(document).ready(function () {
    let allFeedbacks = [];
    // The container is now the <tbody>
    const feedbackContainer = $("#feedback-container");

    // This helper function is unchanged
    function escapeAttribute(s) {
        if (!s) return '';
        return s.toString()
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    //
    // THIS IS THE MAIN UPDATED FUNCTION
    //
    function renderFeedbacks() {
        const sortBy = $("#feedback-sort").val() || 'newest';
        const filterBy = $("#feedback-filter").val() || 'all';

        feedbackContainer.empty();

        // Sorting and filtering logic is unchanged
        let processedFeedbacks = [...allFeedbacks];
        if (filterBy === 'displayed') {
            processedFeedbacks = processedFeedbacks.filter(fb => fb.status === 35);
        } else if (filterBy === 'hidden') {
            processedFeedbacks = processedFeedbacks.filter(fb => fb.status === 34);
        }

        if (sortBy === 'newest') {
            processedFeedbacks.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        } else if (sortBy === 'oldest') {
            processedFeedbacks.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        } else if (sortBy === 'rating-high') {
            processedFeedbacks.sort((a, b) => b.overall_rating - a.overall_rating);
        } else if (sortBy === 'rating-low') {
            processedFeedbacks.sort((a, b) => a.overall_rating - b.overall_rating);
        }

        // Updated "no results" message for the table
        if (processedFeedbacks.length === 0) {
            feedbackContainer.html('<tr><td colspan="7" class="text-center">No feedback matches your criteria.</td></tr>');
            return;
        }

        // Loop and build table rows (<tr>) instead of cards
        processedFeedbacks.forEach((feedback) => {

            // This helper logic is the same as your original
            const photoHtml = feedback.photo
                ? `<div class="card-photo mt-2">
                     <a href="#" class="view-photo-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#imageModal"
                        data-image-url="/storage/app/public/${feedback.photo}">
                        View Uploaded Photo
                     </a>
                   </div>`
                : "";

            const submissionDate = new Date(
                feedback.created_at
            ).toLocaleString("en-US", {
                year: "numeric", month: "long", day: "numeric",
                hour: "2-digit", minute: "2-digit",
            });

            const fullMessage = feedback.message || "No comments provided.";
            const maxLength = 100;
            let messageHtml = "";

            if (fullMessage.length > maxLength) {
                const truncatedMessage = fullMessage.substring(0, maxLength) + "...";
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
            let rowOpacityClass = ""; // Renamed from cardOpacityClass

            if (feedback.status === 35) {
                buttonHtml = `<button class="btn btn-warning btn-sm hide-feedback-btn" data-id="${feedback.id}">Hide</button>`;
                rowOpacityClass = "feedback-displayed";
            } else if (feedback.status === 34) {
                buttonHtml = `<button class="btn btn-success btn-sm display-feedback-btn" data-id="${feedback.id}">Display</button>`;
                rowOpacityClass = "feedback-hidden";
            }

            const statusText = feedback.status === 35 ? 'Displayed' : 'Hidden';

            // HTML for the "Details" cell
            const detailsHtml = `
                <div class="rating-details">
                    <small><strong>Environment:</strong> ${feedback.environment_rating} ★</small>
                    <small><strong>Food:</strong> ${feedback.food_rating} ★</small>
                    <small><strong>Staff:</strong> ${feedback.staff_rating} ★</small>
                </div>
                ${photoHtml}
            `;

            // The new Table Row HTML
            const tableRowHtml = `
                <tr class="${rowOpacityClass}" data-feedback-id="${feedback.id}">
                    <td>${feedback.name}</td>
                    <td><span class="card-rating">${feedback.overall_rating} ★</span></td>
                    <td>${messageHtml}</td>
                    <td>${detailsHtml}</td>
                    <td>
                        <small>
                            ${submissionDate}
                            <br>
                            <strong>Location:</strong> ${feedback.location || "N/A"}
                        </small>
                    </td>
                    <td>${statusText}</td>
                    <td data-btn-container="1">
                        ${buttonHtml}
                    </td>
                </tr>
            `;

            feedbackContainer.append(tableRowHtml);
        });
    }

    //
    // ALL FUNCTIONS BELOW ARE UNCHANGED FROM YOUR ORIGINAL
    //

    function loadFeedbacks(page = 1) {
        $.ajax({
            url: `/customer-service/feedbacks?page=${page}`,
            method: "GET",
            beforeSend: function () {
                // Updated loading message for the table
                feedbackContainer.html('<tr><td colspan="7" class="text-center"><p>Loading feedback...</p></td></tr>');
            },
            success: function (response) {
                if (!response.data || response.data.length === 0) {
                    feedbackContainer.html('<tr><td colspan="7" class="text-center"><p>No feedback has been submitted yet.</p></td></tr>');
                    return;
                }
                allFeedbacks = response.data;
                renderFeedbacks();
            },
            error: function () {
                feedbackContainer.html('<tr><td colspan="7" class="text-center"><p class="text-danger">Failed to load feedback. Please try again later.</p></td></tr>');
            },
        });
    }

    loadFeedbacks(1);

    // Event listeners are unchanged
    $(document).on("change", "#feedback-sort, #feedback-filter", renderFeedbacks);

    $(document).on("click", ".view-photo-btn", function (e) {
        e.preventDefault();
        const imageUrl = $(this).data("image-url");
        $("#modalImage").attr("src", imageUrl);
    });

    $(document).on("click", ".see-more-btn", function (e) {
        e.preventDefault();
        const fullMessage = $(this).data("full-message");
        $("#modalMessageBody").text(fullMessage);
    });

    $(document).on("click", ".hide-feedback-btn", function (e) {
        e.preventDefault();
        const $button = $(this);
        const feedbackId = $button.data("id");
        updateFeedbackStatus($button, feedbackId, 34);
    });

    $(document).on("click", ".display-feedback-btn", function (e) {
        e.preventDefault();
        const $button = $(this);
        const feedbackId = $button.data("id");
        updateFeedbackStatus($button, feedbackId, 35);
    });

    // Update function is unchanged
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
                // Update the local data
                const feedbackToUpdate = allFeedbacks.find(fb => fb.id === feedbackId);
                if (feedbackToUpdate) {
                    feedbackToUpdate.status = newStatus;
                }
                // Re-render the table with the new data
                renderFeedbacks();
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
