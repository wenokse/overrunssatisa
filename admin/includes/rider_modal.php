<!-- rider_modal.php -->
<div class="modal fade" id="assignRider" tabindex="-1" role="dialog" aria-labelledby="assignRiderLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="assignRiderLabel">Assign Rider</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="riderForm">
                    <input type="hidden" name="sales_id" id="sales_id">
                    <div class="form-group">
                        <label for="rider_name" class="col-sm-3 control-label">Rider Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="rider_name" name="rider_name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone_number" class="col-sm-3 control-label">Phone Number</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="rider_address" class="col-sm-3 control-label">Address</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="rider_address" name="rider_address" required></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveRider">Save Rider</button>
            </div>
        </div>
    </div>
</div>
