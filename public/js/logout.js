$(document).on("click", "#logout-btn", function (e) {
    e.preventDefault(); // Prevent the default form submission if wrapped
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to Log out.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Log Out",
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }
        $("#LoadingScreen").fadeIn(200);
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // Get CSRF token from meta tag
            },
            url: "/logout-account", // Logout URL
            type: "POST", // HTTP method
            success: function (data) {
                // console.log("Logout successful", data);

                // Optional: Display a success message with SweetAlert or Toastr
                Swal.fire("Logged Out", "You have been logged out.", "success");

                window.location.href = "/login"; // Redirect to login page
            },
            error: function (xhr, status, error) {
                console.error("Logout failed:", xhr.responseText);
                alert("Logout failed. Please try again.");
            },
        });
    });
});
