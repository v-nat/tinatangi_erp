<div class="modal fade" id="tableModal" tabindex="-1" aria-labelledby="tableModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tableModalLabel">Add New Table</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tableForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="table_id" name="table_id">
                    <input type="hidden" id="form_method" name="_method" value="POST">

                    <div class="mb-3">
                        <label for="name" class="form-label">Table Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback" id="name_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="table_location_id" class="form-label">Table Location <span class="text-danger">*</span></label>
                        <select class="form-select" id="table_location_id" name="table_location_id" required>
                            <option value="">— Select location —</option>
                        </select>
                        <div class="invalid-feedback" id="table_location_id_error"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label">Capacity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
                            <div class="invalid-feedback" id="capacity_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                            <div class="invalid-feedback" id="quantity_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}">
                                        @if($status->id == 1)
                                            Active
                                        @elseif($status->id == 2)
                                            Inactive
                                        @else
                                            {{ $status->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="status_error"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        <div class="invalid-feedback" id="description_error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Table Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="invalid-feedback" id="image_error"></div>
                        <img id="image_preview" src="" alt="Image Preview" class="img-thumbnail mt-2"
                            style="display: none; max-width: 150px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveTableBtn">Save Table</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewTableModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Table Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="viewImage" src="" alt="Table Image" class="img-fluid rounded" style="max-height: 300px;">
                </div>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 30%;">Name</th>
                            <td id="viewName"></td>
                        </tr>
                        <tr>
                            <th>Location</th>
                            <td id="viewLocation"></td>
                        </tr>
                        <tr>
                            <th>Capacity</th>
                            <td id="viewCapacity"></td>
                        </tr>
                        <tr>
                            <th>Quantity</th>
                            <td id="viewQuantity"></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td id="viewStatus"></td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td id="viewDescription"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">Add Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="location_id">
                <div class="mb-3">
                    <label for="location_name" class="form-label">Location Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="location_name" name="location_name"
                        placeholder="e.g. Main Hall" maxlength="255" required>
                    <div class="invalid-feedback" id="location_name_error"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveLocationBtn">Save</button>
            </div>
        </div>
    </div>
</div>
