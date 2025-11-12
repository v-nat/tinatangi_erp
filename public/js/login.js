$(document).ready(function () {
    $("#login_form").on("submit", function (e) {
        e.preventDefault();
        Login();
        return false;
    });

    $(".login-btn").on("click", function (e) {
        e.preventDefault();
        Login();
        return false;
    });

    function Login() {
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
                        $("#LoadingScreen").fadeIn();
                        if (response.user == 'employee') {
                            if (response.position == 16) {
                                window.location.href = "/operations/pos";
                            } else if (response.position == 11 || response.position == 12 || response.position == 14 || response.position == 15) {
                                window.location.href = "/operations/kds";
                            } else {
                                window.location.href = "/executives";
                            }
                        } else if (response.user == 'supplier') {
                            window.location.href = "/supplier";
                        }
                    });
                } else {
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
