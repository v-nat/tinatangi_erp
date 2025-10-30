$(document).ready(function () {
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

                response.data.forEach((feedback) => {
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
                        year: "numeric",
                        month: "long",
                        day: "numeric",
                        hour: "2-digit",
                        minute: "2-digit",
                    });

                    const cardHtml = `
                            <div class="col-md-6 col-lg-4">
                                <div class="feedback-card">
                                    <div class="card-header">
                                        <h5>${feedback.name}</h5>
                                        <span class="card-rating">${
                                            feedback.overall_rating
                                        } ★</span>
                                    </div>
                                    <div class="card-body">
                                        <p><em>"${
                                            feedback.message || "No comments provided."
                                        }"</em></p>

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
                                    </div>
                                </div>
                            </div>
                        `;

                    feedbackContainer.append(cardHtml);
                });
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
});
