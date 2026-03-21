import $ from 'jquery';
import Swal from 'sweetalert2';

$(document).on("click", "#logout-btn", function (e) {
    e.preventDefault();
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
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: "/logout-account",
            type: "POST",
            success: function (data) {
                // console.log("Logout successful", data);
                $("#LoadingScreen").fadeOut(200);

                Toast.fire("Logged Out", "You have been logged out.", "success");

                window.location.href = "/login";
            },
            error: function (xhr, status, error) {
                console.error("Logout failed:", xhr.responseText);
                alert("Logout failed. Please try again.");
            },
        });
    });
});
