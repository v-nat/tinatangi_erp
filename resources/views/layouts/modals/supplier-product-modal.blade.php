<div class="modal fade" id="supplierProductModal" tabindex="-1" aria-labelledby="supplierProductModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="supplierProductModalLabel">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="supplierProductForm">
                @csrf
                <input type="hidden" id="product_id" name="product_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="name" required>
                        <div class="invalid-feedback">Product name is required.</div>
                    </div>
                    <div class="mb-3">
                        <label for="product_category" class="form-label">Category</label>
                        <select class="form-select" id="product_category" name="category_id" required></select>
                        <div class="invalid-feedback">Please select a category.</div>
                    </div>
                    <div class="mb-3">
                        <label for="product_unit" class="form-label">Unit</label>
                        <select class="form-select" id="product_unit" name="unit_id" required></select>
                        <div class="invalid-feedback">Please select a unit.</div>
                    </div>
                    <div class="mb-3">
                        <label for="product_price" class="form-label">Unit Price</label>
                        <input type="number" class="form-control" id="product_price" name="unit_price" min="0" step="0.01" required>
                        <div class="invalid-feedback">Please provide a valid price.</div>
                    </div>
                    <div class="mb-3">
                        <label for="product_status" class="form-label">Status</label>
                        <select class="form-select" id="product_status" name="status">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="product_is_perishable" name="is_perishable" value="1">
                            <label class="form-check-label" for="product_is_perishable">
                                Perishable Item
                                <small class="text-muted d-block">Enable if this product has an expiration date.</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="productSubmitBtn">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
