<div class="modal fade text-left" id="addSupplier" tabindex="-1" role="dialog" aria-labelledby="supplierModalTitle"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title white" id="supplierModalTitle">
                    Add Supplier
                </h5>
            </div>
            <form id="supplierForm" method="POST" action="">
                @csrf
                <input type="hidden" id="supplier_id" name="supplier_id">
                <div class="modal-body">
                    <div class="col-md-12 col-12">
                        <div class="form-group">
                            <label for="supplier_name">Supplier Name</label>
                            <input type="text" id="supplier_name" class="form-control" required
                                placeholder="Enter Name" name="supplier_name">
                            <div class="invalid-feedback">Supplier Name is required.</div>
                        </div>
                    </div>
                    <div class="col-md-12 col-12">
                        <div class="form-group">
                            <label for="email">Supplier Email</label>
                            <input type="email" id="email" class="form-control" required placeholder="example@mail.com"
                                name="email" data-readonly="true">
                            <div class="invalid-feedback">Supplier Email is required.</div>
                        </div>
                    </div>
                    <div class="col-md-12 col-12">
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="tel" id="phone_number" class="form-control" name="phone_number"
                                pattern="^(09|\+639)\d{9}$" maxlength="11" minlength="11" required
                                placeholder="09123456789" value="">
                            <div class="invalid-feedback">Phone Number is required.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="cancel" class="btn btn-light-danger" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button id="submitBtn" type="submit" class="btn btn-primary ml-1">Add Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
