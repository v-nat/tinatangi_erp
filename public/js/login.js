$(document).ready(function () {
    
    $(document).on(
        "click",
        'a[href]:not([target="_blank"]):not([href^="#"])',
        function (e) {
            // Optional: check if it's a same-page anchor or already loading
            var href = $(this).attr("href");
            if (!href || href === "#" || href.startsWith("javascript:")) return;

            // Show loader
            $("#load_screen").fadeIn();

            // Optional: delay navigation for a moment so loader shows clearly
            // Comment out if you want instant navigation
            setTimeout(() => {
                window.location.href = href;
            }, 200);

            // Prevent default to delay navigation (only if using setTimeout)
            e.preventDefault();
        }
    );

    // Handle form submission
    $("#login_form").on("submit", function (e) {
        e.preventDefault();
        Login();
        return false; // Additional prevention
    });

    // Handle login button click
    $(".login-btn").on("click", function (e) {
        e.preventDefault();
        Login();
        return false;
    });

    function Login() {
        // Get form data including CSRF token
        let formData = $("#login_form").serialize();
        // Show loading state
        $(".login-btn")
            .prop("disabled", true)
            .html(
                '<span class="spinner-border" role="status" aria-hidden="true"></span> Loading...'
            );

        $.ajax({
            url: `/login-account`,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: formData,
            dataType: "json",
            success: function (response) {
                if (
                    response.message === "Login successful!" ||
                    response.message === "Login successful"
                ) {
                    Swal.fire({
                        title: "Login Successful!",
                        text: response.message,
                        icon: "success",
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        position: "center",
                        backdrop: true,
                    }).then(() => {
                        // Handle redirect based on user type
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else if (
                            response.role == 4 ||
                            response.role == 2 ||
                            response.role == 3
                        ) {
                            window.location.href = "/admin_index";
                        } else {
                            window.location.href = "/humanresources";
                        }
                    });
                } else {
                    // This handles cases where response is successful but login actually failed
                    Toast.fire({
                        title: "Login Failed",
                        text: response.errors
                            ? Object.values(response.errors)[0]
                            : "Invalid credentials",
                        icon: "error",
                        confirmButtonText: "OK",
                        backdrop: true,
                    });
                }
            },
            error: function (xhr) {
                let errorMessage =
                    "An unexpected error occurred. Please try again.";

                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.errors) {
                        errorMessage = Object.values(response.errors)[0];
                    } else if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error("Error parsing response:", e);
                }

                Toast.fire({
                    title: "Login Failed",
                    text: errorMessage,
                    icon: "error",
                    confirmButtonText: "OK",
                    backdrop: true,
                });
            },
            complete: function () {
                $(".login-btn").prop("disabled", false).text("Login");
            },
        });
    }
});
