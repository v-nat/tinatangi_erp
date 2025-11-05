
<!-- Supplier Process Return Modal -->
<div class="modal fade text-left" id="processReturnModal" tabindex="-1" role="dialog"
    aria-labelledby="processReturnModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger white">
                <h4 class="modal-title" id="processReturnModalLabel">Process Customer Returns</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <form id="processReturnForm">
                <div class="modal-body p-4">
                    @csrf
                    <input type="hidden" id="return_pr_id" name="pr_id">
                    <p class="text-muted">The customer has returned the following items. Please review the reason and
                        photo, then choose an action for each item.</p>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th style="width: 25%;">Reason</th>
                                    <th style="width: 25%;">Photo</th>
                                    <th style="width: 20%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="returnItemsList">
                                <!-- JS will populate this section -->
                                <tr>
                                    <td colspan="4" class="text-center">Loading returned items...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>
                    <button type="submit" class="btn btn-danger ml-1" id="submitReturnProcessing">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Submit Actions</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
