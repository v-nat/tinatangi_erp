import { reloadTable } from "./utils/reloadTable.js";

$(document).ready(function () {
    const $supplierModal = $("#addSupplier");
    const $supplierForm = $("#supplierForm");
    const $supplierModalTitle = $("#supplierModalTitle");
    const $submitBtn = $("#submitBtn");
    const $cancelBtn = $("#cancel");

    let isEditMode = false;
    let editingSupplierId = null;

    function resetForm() {
        $supplierForm[0].reset();
        $supplierForm.find(".is-invalid").removeClass("is-invalid");
        $supplierForm.find(".invalid-feedback").hide();
        $supplierForm.find("input").each(function () {
            this.setCustomValidity("");
        });
        $supplierForm.find("#supplier_id").val("");
        $supplierModalTitle.text("Add Supplier");
        $submitBtn.text("Add Supplier");
        $supplierForm.find("#email").prop("readonly", false);
        isEditMode = false;
        editingSupplierId = null;
    }

    $("#add_supplier").on("click", function (e) {
        e.preventDefault();
        resetForm();
        $supplierModal.modal("show");
    });

    $cancelBtn.on("click", function () {
        resetForm();
    });

    $submitBtn.on("click", function (e) {
        e.preventDefault();
        let isValid = true;

        $supplierForm.find("input[required]").each(function () {
            const $field = $(this);
            const value = $field.val();
            if (!$field.prop("required")) {
                return;
            }
            if (!value || !value.trim()) {
                $field.addClass("is-invalid");
                isValid = false;
            } else {
                $field.removeClass("is-invalid");
            }
        });

        if (!isValid) {
            return;
        }

        const formData = new FormData($supplierForm[0]);
        let requestUrl = "/procurement/supplier/add-supplier";
        let requestMethod = "POST";
        let confirmationText = "Are you sure the credentials for this supplier are correct?";
        let successMessage = "Supplier added successfully!";

        if (isEditMode && editingSupplierId) {
            requestUrl = `/procurement/supplier/${editingSupplierId}`;
            requestMethod = "POST";
            formData.append("_method", "PUT");
            confirmationText = "Save changes to this supplier?";
            successMessage = "Supplier updated successfully!";
        }

        Swal.fire({
            title: "Confirm Information",
            text: confirmationText,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Confirm",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#LoadingScreen").fadeIn(200);
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    url: requestUrl,
                    type: requestMethod,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $("#LoadingScreen").fadeOut(200);
                        reloadTable("supplier_table");
                        resetForm();
                        $supplierModal.modal("hide");
                        Toast.fire({
                            title: "Success!",
                            text: response.message || successMessage,
                            icon: "success",
                        });
                    },
                    error: function (xhr) {
                        $("#LoadingScreen").fadeOut(200);
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach((field) => {
                                const $input = $supplierForm.find(`#${field}`);
                                if ($input.length) {
                                    $input.addClass("is-invalid");
                                    const $feedback = $input.siblings(".invalid-feedback");
                                    if ($feedback.length) {
                                        $feedback.text(errors[field][0]).show();
                                    }
                                }
                            });
                            Toast.fire({
                                title: "Validation Error",
                                text: "Please check the highlighted fields.",
                                icon: "error",
                            });
                        } else if (xhr.responseJSON?.error) {
                            Toast.fire({
                                title: "Error",
                                text: xhr.responseJSON.error,
                                icon: "error",
                            });
                        } else {
                            Toast.fire({
                                title: "Error",
                                text: "An unexpected error occurred.",
                                icon: "error",
                            });
                        }
                    },
                });
            }
        });
    });

    const supplierTable = $("#supplier_table").DataTable({
        scrollX: false,
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
                width: "45px",
            },
            { data: "name", className: "dt-left" },
            { data: "email", className: "dt-left" },
            { data: "phone_number", className: "dt-left" },
            {
                data: "status",
                render: function (data) {
                    const statusText = data || "inactive";
                    const isActive = statusText.toLowerCase() === "active";
                    const badgeClass = isActive ? "bg-success" : "bg-danger";
                    return `<span class="badge ${badgeClass}">${escapeHtml(statusText)}</span>`;
                },
                className: "text-center",
                width: "150px",
            },
            {
                data: "supplier_id",
                render: function (data, type, row) {
                    return `
                    <div>
                        <button type="button" class="btn icon btn-primary btn-edit bs-tooltip me-2" data-id="${data}" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    </div>
                    `;
                },
                width: "150px",
                className: "text-center",
            },
        ],
    });

    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return "";
        }
        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    $("#btn-refresh-suppliers").on("click", function () {
        reloadTable("supplier_table");
    });

    $("#supplier_table").on("click", ".btn-edit", function (e) {
        e.preventDefault();
        const rowData = supplierTable.row($(this).closest("tr")).data();
        if (!rowData) {
            Toast.fire({
                title: "Error",
                text: "Unable to load supplier details for editing.",
                icon: "error",
            });
            return;
        }

        isEditMode = true;
        editingSupplierId = rowData.supplier_id;
        $supplierModalTitle.text("Edit Supplier");
        $submitBtn.text("Update Supplier");

        $("#supplier_id").val(rowData.supplier_id);
        $("#supplier_name").val(rowData.name ?? "");
        $("#email").val(rowData.email ?? "").prop("readonly", true);
        $("#phone_number").val(rowData.phone_number ?? "");

        $supplierModal.modal("show");
    });
});
