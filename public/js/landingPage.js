$(document).ready(function () {
    var food_rating = raterJs({
        starSize: 28,
        step: 0.5,
        element: document.querySelector("#food-rater"),
        rateCallback: function rateCallback(rating, done) {
            this.setRating(rating);
            done();
        },
    });
    var staff_rating = raterJs({
        starSize: 28,
        step: 0.5,
        element: document.querySelector("#staff-rater"),
        rateCallback: function rateCallback(rating, done) {
            this.setRating(rating);
            done();
        },
    });
    var environment_rating = raterJs({
        starSize: 28,
        step: 0.5,
        element: document.querySelector("#environment-rater"),
        rateCallback: function rateCallback(rating, done) {
            this.setRating(rating);
            done();
        },
    });

    $("#submitFeedback").on("click", function (e) {
        e.preventDefault();
        let isValid = true;
        const form_validation = $("#serviceFeedbackForm");
        const staff_ratings = staff_rating.getRating();
        const food_ratings = food_rating.getRating();
        const environment_ratings = environment_rating.getRating();

        form_validation.find(".is-invalid").removeClass("is-invalid");

        form_validation.find("input[required], select[required], textarea[required]").each(
            function () {
                if (!$(this).val()) {
                    $(this).addClass("is-invalid");
                    isValid = false;
                }
            }
        );

        if (
            staff_ratings === null ||
            food_ratings == null ||
            environment_ratings == null
        ) {
            Toast.fire({
                icon: "warning",
                text: "Please fill and rate all fields before submitting.",
            });
            isValid = false;
        }

        if (!isValid) {
            return;
        }

        const overall_rating =
            (staff_ratings + food_ratings + environment_ratings) / 3;

        const form = $("#serviceFeedbackForm");
        let formData = new FormData(form[0]);

        formData.append("food_rating", food_ratings);
        formData.append("staff_rating", staff_ratings);
        formData.append("environment_rating", environment_ratings);
        formData.append("overall_rating", overall_rating.toFixed(2));

        $.ajax({
            url: "/customer-service/submit-feedback",
            method: "POST",
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#LoadingScreen").fadeIn(200);
            },
            success: function (response) {
                $("#LoadingScreen").fadeOut(200);
                Toast.fire({
                    icon: "success",
                    text: response.message || "Thank you for your feedback!",
                });
                form[0].reset();
                food_rating.clear();
                staff_rating.clear();
                environment_rating.clear();
            },
            error: function (xhr) {
                $("#LoadingScreen").fadeOut(200);
                const errors = xhr.responseJSON.errors;
                let errorMessage = "An error occurred. Please try again.";
                if (errors) {
                    errorMessage = Object.values(errors).flat().join("\n");
                }
                Toast.fire({
                    icon: "error",
                    text: errorMessage,
                });
            },
        });
    });
});
