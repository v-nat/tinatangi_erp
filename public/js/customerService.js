$(document).ready(function() {
    function loadFeedbacks(page = 1) {
        const feedbackContainer = $("#feedback-container");

        $.ajax({
            url: `/customer-service/feedbacks?page=${page}`, // Assumes your route is /admin/feedbacks
            method: 'GET',
            beforeSend: function() {
                feedbackContainer.html("<p>Loading feedback...</p>"); // Show loading message
            },
            success: function(response) {
                feedbackContainer.empty(); // Clear the loading message

                if (!response.data || response.data.length === 0) {
                    feedbackContainer.html("<p>No feedback has been submitted yet.</p>");
                    return;
                }

                response.data.forEach(feedback => {
                    const photoHtml = feedback.photo
                        ? `<div class="card-photo mt-2"><a href="/storage/public/${feedback.photo}" target="_blank">View Uploaded Photo</a></div>`
                        : '';

                    const submissionDate = new Date(feedback.created_at).toLocaleString('en-US', {
                        year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
                    });

                    const cardHtml = `
                        <div class="col-md-6 col-lg-4">
                            <div class="feedback-card">
                                <div class="card-header">
                                    <h5>${feedback.name}</h5>
                                    <span class="card-rating">${feedback.overall_rating} ★</span>
                                </div>
                                <div class="card-body">
                                    <p><strong>Email:</strong> ${feedback.email || 'N/A'}</p>
                                    <p><em>"${feedback.message || 'No comments provided.'}"</em></p>
                                    ${photoHtml}
                                </div>
                                <div class="card-footer">
                                    <strong>Location:</strong> ${feedback.location || 'N/A'}<br>
                                    <strong>IP:</strong> ${feedback.ip_address || 'N/A'}<br>
                                    <small>Submitted on: ${submissionDate}</small>
                                </div>
                            </div>
                        </div>
                    `;

                    feedbackContainer.append(cardHtml);
                });

                // (Optional but Recommended) Render Laravel's pagination links
                // You would need a separate function to parse `response.links`
                // and build the pagination UI in the `#pagination-links` div.
            },
            error: function() {
                feedbackContainer.html("<p class='text-danger'>Failed to load feedback. Please try again later.</p>");
            }
        });
    }

    // Initial load of the first page
    loadFeedbacks(1);
});
