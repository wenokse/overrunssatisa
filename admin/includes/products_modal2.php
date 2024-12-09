<!-- Delete -->
<div class="modal fade" id="delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Deleting...</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="products_delete">
                <input type="hidden" class="prodid" name="id">
                <div class="text-center">
                    <p>DELETE PRODUCT</p>
                    <h2 class="bold name"></h2>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-danger btn-flat" name="delete"><i class="fa fa-trash"></i> Delete</button>
              </form>
            </div>
        </div>
    </div>
</div>
<style>
  .modal-lg {
    max-width: 1100px; 
    width: 100%;
}
</style>
<!-- Edit -->
<div class="modal fade" id="edit">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Edit Product</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="products_edit" enctype="multipart/form-data">
                <input type="hidden" class="prodid" name="id">
                <div class="form-group">
                  <label for="edit_name" class="col-sm-1 control-label">Name</label>

                  <div class="col-sm-5">
                    <input type="text" class="form-control" id="edit_name" name="name">
                  </div>

                  <label for="edit_category" class="col-sm-1 control-label">Category</label>

                  <div class="col-sm-5">
                    <select class="form-control" id="edit_category" name="category">
                      <option selected id="catselected"></option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="edit_price" class="col-sm-1 control-label">Price</label>

                  <div class="col-sm-5">
                    <input type="text" class="form-control" id="edit_price" name="price">
                  </div>

                  <label for="edit_stock" class="col-sm-1 control-label">Stock</label>

                  <div class="col-sm-5">
                    <input type="text" class="form-control" id="edit_stock" name="stock">
                  </div>
                </div>
                <div id="edit_color-fields">
                <div class="form-group color-field">
                  <label for="color1" class="col-sm-1 control-label">Color</label>
                  <div class="col-sm-5">
                    <input type="text" class="form-control" name="edit_colors[]">
                  </div>
                  <label for="color_photo1" class="col-sm-1 control-label">Photo</label>
                  <div class="col-sm-4">
                    <input type="file" name="edit_color_photos[]">
                  </div>
                </div>
              </div>
              <button type="button" id="add-edit-color" class="btn btn-info btn-flat"><i class="fa fa-plus"></i> Add Color</button>
             <br><br> <div id="edit_size-fields">
                <div class="form-group size-field">
                  <label for="size1" class="col-sm-1 control-label">Size</label>
                  <div class="col-sm-5">
                    <input type="text" class="form-control" name="edit_sizes[]" placeholder="Enter size (e.g. S, M, L, 42, 44)">
                  </div>
                </div>
              </div>
              <button type="button" id="add-edit-size" class="btn btn-info btn-flat"><i class="fa fa-plus"></i> Add Size</button>

               
                <p><b>Description</b></p>
                <div class="form-group">
                  <div class="col-sm-12">
                    <textarea id="editor2" name="description" rows="10" cols="80"></textarea>
                  </div>
                  
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-success btn-flat" name="edit"><i class="fa fa-check-square-o"></i> Update</button>
              </form>
            </div>
        </div>
    </div>
</div>

