$(document).ready(function () {
    const id = $("#employee_id").val();
    function formatDate(dateString) {
        const options = { year: "numeric", month: "long", day: "numeric" };
        return new Date(dateString).toLocaleDateString("en-US", options);
    }
    $("#orderRequest").DataTable({
        columns: [
            {
                data: "null",
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                className: "text-center",
            },
            {
                data: "item",
            },
            {
                data: "qnty",
            },
            {
                data: "unit",
                render: function (data) {
                    return "₱" + parseFloat(data).toFixed(2);
                },
            },
            {
                data: "total",
                render: function (data) {
                    return "₱" + parseFloat(data).toFixed(2);
                },
            },

            {
                data: "action",
            },
        ],
    });

    function getSuppliers() {
        const supplierSelect = document.getElementById("supplier");
        
        fetch(`/procurement/supplier/get-active-supplier`)
            .then((response) => response.json())
            .then((data) => {
                supplierSelect.innerHTML =
                    '<option value="" disabled selected>Choose...</option>';
                data.forEach((s) => {
                    const option = document.createElement("option");
                    option.value = s.id;
                    option.textContent = s.supplier_name;
                    supplierSelect.appendChild(option);
                });
            });
    }

    getSuppliers();

    function reloadTable(tableId) {
        $("#" + tableId)
            .DataTable()
            .ajax.reload(null, false);
    }
});
