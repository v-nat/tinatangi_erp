$(document).ready(function () {
    $("#add_supplier").on("click", function (e) {
        e.preventDefault();
        $("#addSupplier").modal("show");
    });
    $("#cancel").click(function (e) {
        e.preventDefault();
        const $form = $("#supplierForm");
        $form.find("input").val("");
        $form.find(".is-invalid").removeClass("is-invalid");
        $form.find(".invalid-feedback").remove();
    });

    $("#submitBtn").on("click", function (e) {
        e.preventDefault();
        let isValid = true;
        const $form = $("#supplierForm");

        $form.find("input").each(function () {
            const $field = $(this);
            const value = $field.val();
            if ($field.prop("required") && (!value || !value.trim())) {
                $field.addClass("is-invalid");
                isValid = false;
            } else {
                $field.removeClass("is-invalid");
            }
        });

        if (isValid) {
            $("#addSupplier").modal("hide");
            let formData = new FormData($("#supplierForm")[0]);
            Swal.fire({
                title: "Confirm Information",
                text: "Are you sure the credentials for this supplier is correct?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Confirm",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#LoadingScreen").fadeIn(200);
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        url: "/procurement/supplier/add-supplier",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            $("#LoadingScreen").fadeOut(200);
                            reloadTable("supplier_table");
                            Toast.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                            });
                        },
                        error: function (xhr) {
                            // console.error('Error response:', xhr);
                            $("#LoadingScreen").fadeOut(200);
                            if (xhr.responseJSON?.errors) {
                                let errorMessages = Object.values(
                                    xhr.responseJSON.errors
                                )
                                    .flat()
                                    .join("\n");
                                Toast.fire(
                                    "Validation Error",
                                    errorMessages,
                                    "error"
                                );
                            } else {
                                Toast.fire(
                                    "Error",
                                    "An unexpected error occurred.",
                                    "error"
                                );
                            }
                        },
                    });
                }
            });
        }
    });
    function reloadTable(tableId) {
        $("#" + tableId)
            .DataTable()
            .ajax.reload(null, false);
    }
    $("#supplier_table").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/procurement/supplier/get-supplier",
            type: "GET",
            dataSrc: "data",
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                className: "text-center",
            },
            { data: "name" },
            { data: "email" },
            { data: "phone_number", className: "text-start" },
            {
                data: "status",
                render: function (data) {
                    return `<span class="badge bg-light-success ${
                        data === "active" ? "success" : "danger"
                    }">${data}</span>`;
                },
            },
            {
                data: "supplier_id",
                render: function (data, type, row) {
                    return `
                    <div>
                        <a href="#" class="btn icon btn-primary btn-edit bs-tooltip me-2"
                           data-id="${data}"
                           data-name="${row.name}"
                           title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                    </div>
                        `;
                },
            },
        ],
    });
});
