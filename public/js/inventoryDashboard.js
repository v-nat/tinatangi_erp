import {
    buildInvoiceModal,
    printInvoice,
} from "./utils/modal_builders_scm.js";

$(document).ready(function () {
    const ALERT_LIMIT = 3;

    const ALERT_CONTAINER_ID = "#invClaims";

    let allPurchaseRequests = [];

    function getToReceiveRequests() {
        fetch(`/inventory/get-to-receive`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        "Network response was not ok: " + response.statusText
                    );
                }
                return response.json();
            })
            .then((response) => {
                // Store the full list of requests globally
                allPurchaseRequests = response.data || [];
                $(ALERT_CONTAINER_ID).empty();
                // Pass the initial limit to show only 5 requests
                buildReceiveAlerts(allPurchaseRequests, ALERT_LIMIT);
            })
            .catch((error) => {
                console.error("Error fetching 'To Receive' data:", error);
                $(ALERT_CONTAINER_ID).html(
                    `<div class="alert alert-danger">Error loading pending receipts.</div>`
                );
            });
    }

    /**
     * Builds and inserts HTML alerts for pending Purchase Requests.
     * @param {Array<Object>} requests - Array of Purchase Request objects.
     * @param {number} [limit=requests.length] - The maximum number of requests to display.
     */
    function buildReceiveAlerts(requests, limit = requests.length) {
        // Clear the container before rendering
        $(ALERT_CONTAINER_ID).empty();

        if (!Array.isArray(requests) || requests.length === 0) {
            $(ALERT_CONTAINER_ID).html(
                `<div class="alert alert-light-success">No purchase requests are currently ready for receiving.</div>`
            );
            return;
        }

        const requestsToDisplay = requests.slice(0, limit);
        const hasMore = requests.length > limit;

        requestsToDisplay.forEach((request) => {
            const invoiceId = request.invoice_id;
            const requestId = request.id;
            const supplierName = request.supplier_name || "N/A";

            let totalItemsQuantity = 0;

            if (
                request.purchase_orders &&
                Array.isArray(request.purchase_orders)
            ) {
                request.purchase_orders.forEach((po) => {
                    if (po.details && Array.isArray(po.details)) {
                        po.details.forEach((detail) => {
                            totalItemsQuantity += detail.quantity || 0;
                        });
                    }
                });
            }

            const alertHtml = `
                <div class="alert alert-light-success alert-dismissible fade show" role="alert">
                    <div class="row align-items-center">
                        <div class="col-8 col-lg-8 col-md-8 justify-content-start d-flex">
                            <div class="d-block">
                                <h6>PR NO: ${requestId}</h6>
                                <p class="mb-1 ">From: ${supplierName}</p>
                                <p class="mb-0 ">Total Item(s): ${totalItemsQuantity}</p>
                            </div>
                        </div>
                        <div class="col-4 col-lg-4 col-md-4 p-0 justify-content-end align-items-center d-flex">
                            <a href="#" class="btn icon btn-sm btn-info btn-invoice bs-tooltip me-2" data-id="${invoiceId}" title="View Request">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-success btn-receive bs-tooltip me-2" data-id="${requestId}" data-qnty="${totalItemsQuantity}" title="Receive Inventory">
                                <i class="fa-solid fa-box-open"></i>
                            </a>
                        </div>
                    </div>
                </div>
            `;

            $(ALERT_CONTAINER_ID).append(alertHtml);
        });

        // Add the "View All" link if more items are available
        if (hasMore) {
            const remainingCount = requests.length - limit;
            const viewAllHtml = `
                <div class="text-center mt-3" id="viewAllContainer">
                    <a href="#" class="btn btn-sm btn-outline-info btn-show-all">
                        View All ${remainingCount} More Requests
                    </a>
                </div>
            `;
            $(ALERT_CONTAINER_ID).append(viewAllHtml);
        }
    }

    $(document).on("click", ".btn-receive", function () {
        const id = $(this).data("id");
        const qnty = $(this).data("qnty");
        Swal.fire({
            title: "Are you sure?",
            text: `You are about to receive inventory for Purchase Order No: ${id}.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Receive it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#LoadingScreen").fadeIn(200);
                $.post(`/inventory/item-to-receive/receive-inventory/${id}/${qnty}`, function (response) {
                    if (response.success) {
                        Swal.fire(
                            "Received!",
                            "The inventory has been successfully received.",
                            "success"
                        );
                        // Refresh the alerts
                        getToReceiveRequests();
                    } else {
                        Swal.fire("Error", response.error || "Failed to receive inventory.", "error");
                    }
                })
                    .fail(function (xhr) {
                        const errorMsg = xhr.responseJSON
                            ? xhr.responseJSON.error
                            : "Failed to process the request.";
                        Swal.fire("Error", errorMsg, "error");
                    })
                    .always(function () {
                        $("#LoadingScreen").fadeOut(200);
                    });
            }
        });
    });

    $(document).on("click", ".btn-show-all", function (e) {
        e.preventDefault();
        if (allPurchaseRequests.length > 0) {
            buildReceiveAlerts(allPurchaseRequests, allPurchaseRequests.length);
        } else {
            console.error("Full request list is not available.");
        }
    });

    getToReceiveRequests();

    $(document).on("click", ".btn-invoice", function () {
        const id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $.get(`/inventory/item-to-receive/get-invoice/${id}`, function (response) {
            if (response.data) {
                const requestData = response.data;
                buildInvoiceModal(requestData);
            } else {
                alert("Error: Invoice not found.");
            }
        })
            .fail(function (xhr) {
                const errorMsg = xhr.responseJSON
                    ? xhr.responseJSON.error
                    : "Failed to load purchase invoice details.";
                alert(errorMsg);
            })
            .always(function () {
                $("#LoadingScreen").fadeOut(200);
            });
    });

    $(document).on("click", "#print", function () {
        printInvoice();
    });

});
