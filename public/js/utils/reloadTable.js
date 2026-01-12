export function reloadTable(tableId) {
    $("#" + tableId)
        .DataTable()
        .ajax.reload(null, false);
}
