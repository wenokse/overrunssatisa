<!-- trace_modal.php -->
<div class="modal fade" id="traceModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b>Trace Order</b></h4>
            </div>
            <div class="modal-body">
                <p><b>Date: <span id="trace_date"></span></b>
                <b class="pull-right">Transaction ID: <span id="trace_transid"></span></b></p><br>
                <p><b>Order Details:</b> <h5 id="trace_detail"></h5>
               <p><b>Total Amount:</b> <span id="trace_total"></span></p>
                <p><b>Status:</b> <span id="trace_status"></span></p>
                <p><b>Address:</b> <span id="trace_address"></span></p>
                <p><b>Distance:</b> <span id="trace_distance">10 km</span></p>
                <p><b>Estimated Time:</b> <span id="trace_time"></span></p>
                <p><b>Product Images:</b></p>
                <div id="trace_images" style="display: flex; flex-wrap: wrap;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<style>
     .modal-content {
    border-radius: 20px; 
}

</style>