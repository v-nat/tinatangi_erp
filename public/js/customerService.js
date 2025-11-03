$(document).ready(function () {
    let allFeedbacks = [];
    const feedbackContainer = $("#feedback-container");

    function escapeAttribute(s) {
        if (!s) return '';
        return s.toString()
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
    }


    function renderFeedbacks() {
        const sortBy = $("#feedback-sort").val() || 'newest';
        const filterBy = $("#feedback-filter").val() || 'all';

        feedbackContainer.empty();

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

        if (processedFeedbacks.length === 0) {
            feedbackContainer.html("<p>No feedback matches your criteria.</p>");
            return;
        }

        processedFeedbacks.forEach((feedback) => {
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
            let cardOpacityClass = "";

            if (feedback.status === 35) {
                buttonHtml = `<button class="btn btn-warning btn-sm hide-feedback-btn" data-id="${feedback.id}">Hide</button>`;
                cardOpacityClass = "feedback-displayed";
            } else if (feedback.status === 34) {
                buttonHtml = `<button class="btn btn-success btn-sm display-feedback-btn" data-id="${feedback.id}">Display</button>`;
                cardOpacityClass = "feedback-hidden";
            }

            const cardHtml = `
                <div class="col-md-6 col-lg-4 ${cardOpacityClass}" data-feedback-id="${feedback.id}">
                    <div class="feedback-card h-100">
                        <div class="card-header">
                            <h5>${feedback.name}</h5>
                            <span class="card-rating">${feedback.overall_rating} ★</span>
                        </div>
                        <div class="card-body">
                            ${messageHtml}
                            <div class="rating-details mt-3">
                                <p class="mb-1"><strong>Environment:</strong> ${feedback.environment_rating} ★</p>
                                <p class="mb-1"><strong>Food:</strong> ${feedback.food_rating} ★</p>
                                <p class="mb-1"><strong>Staff:</strong> ${feedback.staff_rating} ★</p>
                            </div>
                            ${photoHtml}
                        </div>
                        <div class="card-footer">
                            <strong>Location:</strong> ${feedback.location || "N/A"}<br>
                            <strong>IP:</strong> ${feedback.ip_address || "N/A"}<br>
                            <small>Submitted on: ${submissionDate}</small>
                            <div class="mt-2 text-end" data-btn-container="1">
                                ${buttonHtml}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            feedbackContainer.append(cardHtml);
        });
    }


    function loadFeedbacks(page = 1) {
        $.ajax({
            url: `/customer-service/feedbacks?page=${page}`,
            method: "GET",
            beforeSend: function () {
                feedbackContainer.html("<p>Loading feedback...</p>");
            },
            success: function (response) {
                if (!response.data || response.data.length === 0) {
                    feedbackContainer.html("<p>No feedback has been submitted yet.</p>");
                    return;
                }
                allFeedbacks = response.data;
                renderFeedbacks();
            },
            error: function () {
                feedbackContainer.html("<p class='text-danger'>Failed to load feedback. Please try again later.</p>");
            },
        });
    }

    loadFeedbacks(1);


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
                const feedbackToUpdate = allFeedbacks.find(fb => fb.id === feedbackId);
                if (feedbackToUpdate) {
                    feedbackToUpdate.status = newStatus;
                }
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
