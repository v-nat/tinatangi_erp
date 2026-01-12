$(document).ready(function () {
    var loginResponseData = null;
    var isUpdatingPassword = false;

    function updateCsrfToken(token) {
        if (token) {
            $('meta[name="csrf-token"]').attr("content", token);
        }
    }

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

    $("#force_new_password, #force_confirm_password").on("input", function () {
        $("#forcePasswordError").addClass("d-none").text("");
        updatePasswordChecklist();
    });

    $("#forcePasswordSubmit").on("click", function (e) {
        e.preventDefault();
        submitForcedPasswordChange();
        return false;
    });

    function Login() {
        var formData = $("#login_form").serialize();

        $(".login-btn")
            .prop("disabled", true)
            .html(
                '<span class="spinner-border" role="status" aria-hidden="true"></span> Loading...'
            );

        $.ajax({
            url: "/login-account",
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
                    if (response.csrfToken) {
                        updateCsrfToken(response.csrfToken);
                    }
                    loginResponseData = response;
                    var successMessage = response.must_change_password
                        ? "Login successful! Please update your password to continue."
                        : response.message;

                    Swal.fire({
                        title: "Login Successful!",
                        text: successMessage,
                        icon: "success",
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        position: "center",
                        backdrop: true,
                    }).then(function () {
                        if (response.must_change_password) {
                            openForcePasswordModal();
                        } else {
                            handlePostLoginRedirect(response);
                        }
                    });
                } else {
                    showLoginError(response);
                }
            },
            error: function (xhr) {
                var errorMessage =
                    "An unexpected error occurred. Please try again.";

                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.errors) {
                        errorMessage = Object.values(response.errors)[0];
                    } else if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (err) {
                    console.error("Error parsing response:", err);
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

    function showLoginError(response) {
        var message = response.errors
            ? Object.values(response.errors)[0]
            : "Invalid credentials";

        Toast.fire({
            title: "Login Failed",
            text: message,
            icon: "error",
            confirmButtonText: "OK",
            backdrop: true,
        });
    }

    function openForcePasswordModal() {
        $("#forcePasswordForm")[0].reset();
        $("#forcePasswordError").addClass("d-none").text("");
        $("#forcePasswordBackdrop").removeClass("d-none").addClass("show");
        $("#forcePasswordModal").addClass("show").css("display", "block");
        $("body").addClass("modal-open").css("padding-right", "0");
        $("#force_new_password").trigger("focus");
        $("#login_form :input").prop("disabled", true);
        $(".login-btn").prop("disabled", true);
    }

    function closeForcePasswordModal() {
        $("#forcePasswordModal").removeClass("show").css("display", "none");
        $("#forcePasswordBackdrop").addClass("d-none").removeClass("show");
        $("body").removeClass("modal-open").css("padding-right", "");
        $("#login_form :input").prop("disabled", false);
        $(".login-btn").prop("disabled", false).text("Login");
    }

    function isStrongPassword(password) {
        var pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
        return pattern.test(password);
    }

    function updatePasswordChecklist() {
        var password = $.trim($("#force_new_password").val());
        var confirmPassword = $.trim($("#force_confirm_password").val());

        var rules = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /\d/.test(password),
            special: /[^A-Za-z0-9]/.test(password),
            match: password.length > 0 && password === confirmPassword,
        };

        $.each(rules, function (rule, passed) {
            var $item = $("#forcePasswordChecklist li[data-rule='" + rule + "']");
            if (!$item.length) {
                return;
            }

            var $icon = $item.find("i");

            if (passed) {
                $item.removeClass("text-danger").addClass("text-success");
                $icon.removeClass("bi-x-circle-fill").addClass("bi-check-circle-fill");
            } else {
                $item.removeClass("text-success").addClass("text-danger");
                $icon.removeClass("bi-check-circle-fill").addClass("bi-x-circle-fill");
            }
        });
    }

    function submitForcedPasswordChange() {
        if (isUpdatingPassword) {
            return;
        }

        var newPassword = $.trim($("#force_new_password").val());
        var confirmPassword = $.trim($("#force_confirm_password").val());
        var $errorBox = $("#forcePasswordError");
        var $submitBtn = $("#forcePasswordSubmit");

        if (newPassword.length === 0 || confirmPassword.length === 0) {
            $errorBox.text("Please complete all fields.").removeClass("d-none");
            return;
        }

        if (!isStrongPassword(newPassword)) {
            $errorBox
                .text(
                    "Password must be at least 8 characters and include uppercase, lowercase, number, and special character."
                )
                .removeClass("d-none");
            updatePasswordChecklist();
            return;
        }

        if (newPassword !== confirmPassword) {
            $errorBox.text("Passwords do not match.").removeClass("d-none");
            updatePasswordChecklist();
            return;
        }

        isUpdatingPassword = true;
        $submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
            );

        $.ajax({
            url: "/force-update-password",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                new_password: newPassword,
                new_password_confirmation: confirmPassword,
            },
            dataType: "json",
            success: function (response) {
                closeForcePasswordModal();
                if (response.csrfToken) {
                    updateCsrfToken(response.csrfToken);
                }
                Swal.fire({
                    title: "Password Updated",
                    text: response.message || "Your password has been updated successfully.",
                    icon: "success",
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                }).then(function () {
                    if (loginResponseData) {
                        loginResponseData.must_change_password = false;
                        handlePostLoginRedirect(loginResponseData);
                    } else {
                        window.location.href = "/login";
                    }
                });
            },
            error: function (xhr) {
                var message = "Failed to update password. Please try again.";

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors)[0];
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                $errorBox.text(message).removeClass("d-none");
            },
            complete: function () {
                isUpdatingPassword = false;
                $submitBtn.prop("disabled", false).text("Update Password");
            },
        });
    }

    function handlePostLoginRedirect(response) {
        $("#LoadingScreen").fadeIn();
        if (response.user === "employee") {
            if (response.position === 16) {
                window.location.href = "/operations/pos";
                return;
            }

            if (
                response.position === 11 ||
                response.position === 12 ||
                response.position === 14 ||
                response.position === 15
            ) {
                window.location.href = "/operations/kds";
                return;
            }

            window.location.href = "/executives";
            return;
        }

        if (response.user === "supplier") {
            window.location.href = "/supplier";
            return;
        }

        window.location.href = "/executives";
    }
});
