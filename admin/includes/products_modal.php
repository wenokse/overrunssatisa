<!-- Description -->
<div class="modal fade" id="description">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b><span class="name"></span></b></h4>
            </div>
            <div class="modal-body">
                <p id="desc"></p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add -->
<div class="modal fade" id="addnew">
      <div class="modal-dialog modal-lg-custom"> 
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Add New Product</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="products_add" enctype="multipart/form-data">
                <div class="form-group">
                  <label for="name" class="col-sm-1 control-label">Name</label>
                  <div class="col-sm-5">
                    <input type="text" class="form-control" id="name" name="name" required>
                  </div>
                  <label for="category" class="col-sm-1 control-label">Category</label>
                  <div class="col-sm-5">
                    <select class="form-control" id="category" name="category" required>
                      <option selected disabled>Select Category</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="price" class="col-sm-1 control-label">Price</label>
                  <div class="col-sm-5">
                    <input type="text" class="form-control" id="price" name="price" placeholder="+ 10" required>
                  </div>
                  <label for="photo" class="col-sm-1 control-label">Display</label>
                  <div class="col-sm-5">
                    <input type="file" id="photo" name="photo">
                  </div>
                </div>
                
                <!-- Color Fields -->
                <div id="color-fields">
                  <div class="form-group color-field">
                    <label for="color1" class="col-sm-1 control-label">Color</label>
                    <div class="col-sm-5">
                      <input type="text" class="form-control" name="colors[]" required>
                    </div>
                    <label for="color_photo1" class="col-sm-1 control-label">Photo</label>
                    <div class="col-sm-4">
                      <input type="file" name="color_photos[]" required>
                    </div>
                  </div>
                </div>
                <button type="button" id="add-color" class="btn btn-info btn-flat"><i class="fa fa-plus"></i> Add Color</button>
                <br><br>
                <!-- Size Fields -->
                <div id="size-fields">
                  <div class="form-group size-field">
                    <label for="size1" class="col-sm-1 control-label">Size</label>
                    <div class="col-sm-5">
                      <input type="text" class="form-control" name="sizes[]" placeholder="Enter size (e.g. S, M, L, 42, 44)">
                    </div>
                   
                  </div>
                </div>
                <button type="button" id="add-size" class="btn btn-info btn-flat"><i class="fa fa-plus"></i> Add Size</button>

                <p><b>Description</b></p>
                <div class="form-group">
                  <div class="col-sm-12">
                    <textarea id="editor1" name="description" rows="10" cols="80" required></textarea>
                  </div>
                </div>
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['admin']; ?>">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-primary btn-flat" name="add"><i class="fa fa-save"></i> Save</button>
              </form>
            </div>
        </div>
    </div>
</div>
<style>
  .modal-lg-custom {
    max-width: 1100px; 
    width: 100%;
}
</style>
<script>
  document.getElementById('price').addEventListener('input', function (e) {
   this.value = this.value.replace(/[^0-9.]/g, ''); // Allow only numbers and decimal points
});

</script>
<!-- Update Photo -->
<div class="modal fade" id="edit_photo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b><span class="name"></span></b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="products_photo" enctype="multipart/form-data">
                <input type="hidden" class="prodid" name="id">
                <div class="form-group">
                    <label for="photo" class="col-sm-3 control-label">Photo</label>

                    <div class="col-sm-9">
                      <input type="file" id="photo" name="photo" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-success btn-flat" name="upload"><i class="fa fa-check-square-o"></i> Update</button>
              </form>
            </div>
        </div>
    </div>
</div>
