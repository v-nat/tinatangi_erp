<!-- view modal-->
<div class="modal fade text-left w-100" id="viewInvoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel20"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel20">Review Order</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body p-4">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Close</span>
                </button>
                <button id="receiveItem" type="button" class="btn btn-success ml-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Receive</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ////////////////////////////////////// RESSTOCK ///////////////////////////////////////////////////// --}}

<!--warning theme Modal -->
<div class="modal fade text-left" id="stockRequest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title white" id="myModalLabel120">
                    Request Stock
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <h6>
                    Send Stock Request to Procurement Department
                </h6>
                <form id="restockReqForm" class="mt-4">
                    @csrf
                    <input type="hidden" name="item_id" id="req_item_id">
                    <input type="hidden" name="sku" id="req_sku">
                    <div class="row">
                        <div class="col-7">
                            <h6 class="fw-normal">Details:</h6>
                            <h6 class="fw-normal" id="req_item_name"></h6>
                            <h6 class="fw-normal" id="req_unit_price"></h6>
                            <h6 class="fw-normal" id="req_unit"></h6>
                        </div>

                        <div class="col-5">
                            <div class="form-group">
                                <label for="qnty">Quantity</label>
                                <input type="number" id="qnty" class="form-control py-2" min="1" required
                                    placeholder="0" name="qnty">
                                <div class="invalid-feedback">Quantity is required.</div>
                            </div>
                            <h6 class="fw-normal" id="total_price"></h6>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="cancelStockReq" type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cancel</span>
                </button>

                <button id="submit-req-btn" type="button" class="btn btn-warning ml-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Submit</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- /////////////////////////////////////////////// VIEW PRODUCT //////////////////////////////////////// --}}

<div class="modal fade" id="viewProductModal" tabindex="-1" aria-labelledby="viewProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewProductModalLabel">Product Details</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <img src="https://via.placeholder.com/350x350.png?text=No+Image" id="productViewImage" class="img-fluid rounded shadow-sm" alt="Product Image" style="max-height: 350px; width: 100%; object-fit: cover;">
                    </div>
                    <div class="col-md-7">
                        <h3 id="productViewName"></h3>
                        <p class="text-muted" style="font-size: 1.1rem;" id="productViewCategory"></p>
                        <hr>
                        <p><strong>Description:</strong><br>
                            <span id="productViewDescription"></span>
                        </p>
                        <hr>
                        <h4>Base Price: <strong id="productViewPrice"></strong></h4>
                        <p class="mt-3">
                            <strong>Status:</strong>
                            <span class="badge" id="productViewStatus"></span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


{{-- /////////////////////////////////////////////// ADD NEW PRODUCT ////////////////////////////////////// --}}

<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="addProductForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback">Product name is required.</div>
                    </div>
                    <div class="mb-3">
                        <label for="base_price" class="form-label">Base Price</label>
                        <input type="number" class="form-control" id="base_price" name="base_price" step="0.01"
                            required>
                        <div class="invalid-feedback">Please enter a valid, non-negative price.</div>
                    </div>
                    <div class="mb-3">
                        <label for="product_category_id" class="form-label">Category</label>
                        <select class="form-select" id="product_category_id" name="product_category_id" required>
                            <option value="">Loading...</option>
                        </select>
                        <div class="invalid-feedback">Please select a category.</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image</label>
                        <input class="form-control" type="file" id="image" name="image">
                        <div class="invalid-feedback" id="image_error">Invalid file type. Please use jpg, jpeg, or png.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="submitProduct" type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- /////////////////////////////////////////////// MANAGE RECIPE ////////////////////////////////////////////////////
--}}

<div class="modal fade" id="recipeModal" tabindex="-1" aria-labelledby="recipeModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recipeModalLabel">Manage Recipe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- The action URL will be set dynamically by JavaScript --}}
            <form id="recipeForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h5 class="card-title">Recipe for: <strong id="recipe-product-name">Loading...</strong></h5>
                    <hr>
                    <h6>Current Ingredients</h6>
                    <ul class="list-group mb-4" id="current-ingredients-list">
                        <p>Loading recipe...</p>
                    </ul>
                    <hr>
                    <h6>Update Ingredients</h6>
                    <div id="ingredient-list"></div>
                    <button type="button" id="add-ingredient-btn" class="btn btn-primary mt-2">Add Ingredient</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Recipe</button>
                </div>
            </form>
        </div>
    </div>
</div>
