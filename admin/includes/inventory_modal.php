<!-- Edit Stock Out -->
<div class="modal fade" id="edit_stockout">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b>Updating...</b></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="inventory_stockout.php" id="stockForm">
                    <input type="hidden" class="prodid" name="id"> 
                    <div class="text-center">
                        <p>UPDATE STOCK</p>
                        <h2 class="bold name"></h2>
                    </div>
                    <!-- Placeholder for dynamically added input -->
                    <div id="addStockInputContainer"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger btn-flat pull-left" style="border-radius: 8px;" name="stockout"><i class="fa fa-sign-out"></i> Stock Out</button>
                <button type="button" id="addStockButton" class="btn btn-success btn-flat" style="border-radius: 8px;"><i class="fa fa-plus"></i> Add Stock</button>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    .modal-content {
        border-radius: 10px;
    }
</style>
<script>
document.getElementById('addStockButton').addEventListener('click', function() {
    var container = document.getElementById('addStockInputContainer');
    var input = document.getElementById('addStockInput');
    
    if (!input) {
        input = document.createElement('input');
        input.type = 'text';
        input.className = 'form-control';
        input.name = 'addstock';
        input.id = 'addStockInput';
        input.placeholder = 'Enter number to add to stock';
        container.appendChild(input);

        // Add event listener to ensure only numbers can be typed
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, ''); 
        });
    } else if (input.value) {
        document.getElementById('stockForm').submit();
    }
});
</script>


<!-- Edit Stock Out -->
<!-- <div class="modal fade" id="edit_stockout">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Updating...</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="inventory_stockin.php">
                <input type="hidden" class="prodid" name="id"> 
                <div class="text-center">
                    <p>UPDATE STOCK</p>
                    <h2 class="bold name"></h2>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-success btn-flat" name="stockin"><i class="fa fa-sign-in"></i> Stock In</button>
              </form>
            </div>
        </div>
    </div>
</div> -->