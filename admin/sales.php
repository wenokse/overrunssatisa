<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Sales History
      </h1>
      <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Sales</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="">
        <div class="col-xs-13">
          <div class="">
            <div class="box-header with-border">
              <!-- <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> Add Customer</a> -->
              <div class="pull-right">
                <form method="POST" class="form-inline" action="sales_print">
                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right col-sm-8" id="reservation" name="date_range">
                  </div>
                  <button type="button" class="btn btn-success btn-sm btn-flat" id="print-report-btn"><span class="glyphicon glyphicon-print"></span>&nbsp;Report Print</button>
                 
                </form>
                
                
              </div>
            </div>
            <div class="box-body table-responsive">
            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th class="hidden"></th>
                  <th>Date</th>
                  <th>Buyer Name</th>
                  <th>Transaction #</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Full Details</th>
                </thead>
                <tbody>
                <?php
                          $conn = $pdo->open();

                          try {
                            $user_id = isset($_SESSION['admin']) ? $_SESSION['admin'] : null;
                        
                            if ($user_id === null) {
                                die("Error: User not logged in");
                            }
                            $stmt = $conn->prepare("SELECT type FROM users WHERE id = :user_id");
                            $stmt->execute(['user_id' => $user_id]);
                            $user = $stmt->fetch();
                        
                            if (!$user) {
                                die("Error: User not found");
                            }
                        
                            $user_type = $user['type'];
                        
                            if ($user_type == 1) {
                              $stmt = $conn->prepare("SELECT *, sales.id AS salesid, sales.status, 
                              home_address.recipient_name, home_address.address, 
                              home_address.address2, home_address.address3, 
                              home_address.phone 
                            FROM sales 
                            LEFT JOIN home_address ON home_address.sales_id = sales.id
                            ORDER BY sales.status = 4, 3, 2, 1 DESC, sales.sales_date DESC");

                              $stmt->execute();
                          } elseif ($user_type == 2) {
                            $stmt = $conn->prepare("SELECT *, sales.id AS salesid, sales.status, 
                                home_address.recipient_name, home_address.address, 
                                home_address.address2, home_address.address3, 
                                home_address.phone 
                            FROM sales
                            LEFT JOIN home_address ON home_address.sales_id = sales.id
                            WHERE sales.admin_id = :admin_id
                            ORDER BY sales.status = 4, 3, 2, 1 DESC, sales.sales_date DESC");
                              $stmt->execute(['admin_id' => $user_id]);
                          }
                           else {
                                die("Error: Invalid user type");
                            }
                        
                            foreach ($stmt as $row) {
                                $total = 0.00; // Initialize total as float
                                
                                if ($user_type == 1) {
                                    // For admin (type 1) - show admin_sales total
                                    $stmt_details = $conn->prepare("SELECT admin_sales.*, products.name 
                                        FROM admin_sales 
                                        LEFT JOIN products ON products.id=admin_sales.product_id 
                                        WHERE admin_sales.sales_id=:id");
                                    $stmt_details->execute(['id' => $row['salesid']]);
                                    
                                    foreach ($stmt_details as $details) {
                                        $subtotal = floatval($details['admin_price']);
                                        $total += $subtotal;
                                    }
                                } elseif ($user_type == 2) {
                                    // For vendors (type 2) - show vendor_amount
                                    $total = floatval($row['vendor_amount']); // Convert to float
                                    if ($total === null || $total === '') {
                                        $total = 0.00;
                                    }
                                }
                            ?>                        
                                <tr>
                                    <td class='hidden'></td>
                                    <td><?php echo date('M d, Y', strtotime($row['sales_date']))?></td>
                                    <td><?php echo $row['recipient_name'] ?></td>
                                    <td><?php echo $row['pay_id']?></td>
                                    <td>&#8369; <?php echo number_format(floatval($total), 2)?></td>
                            <td>
                              <?php if ($row['status'] == 0) { ?>
                                  <p class="btn-sm text-center" style="background: linear-gradient(to right, #00C9FF, #92FE9D); color: #000; border-radius: 8px;">Pending</p>
                              <?php } ?>
                              <?php if ($row['status'] == 1) { ?>
                                  <p class="btn-sm text-center" style="background: linear-gradient(to right, #39FF14, #B4EC51); color: #000; border-radius: 8px;">Packaging</p>
                              <?php } ?>
                              <?php if ($row['status'] == 2) { ?>
                                  <p class="btn-sm text-center" style="background: linear-gradient(to right, #0072ff, #00c6ff); color: #fff; border-radius: 8px;">Pickup</p>
                              <?php } ?>
                              <?php if ($row['status'] == 3) { ?>
                                  <p class="btn-sm text-center" style="background: linear-gradient(to right, #00C9A7, #FF3F5E); color: #fff; border-radius: 8px;">Delivered</p>
                              <?php } ?>
                              <?php if ($row['status'] == 4) { ?>
                                  <p class="btn-sm text-center" style="background: linear-gradient(to right, #00C9A7, #FF3F5E); color: #fff; border-radius: 8px;">Received</p>
                              <?php } ?>
                          </td>

                            <td>
                            <button type='button' class='btn btn-info btn-sm btn-flat transact' 
                                  style='border-radius: 8px;'
                                  data-id="<?php echo $row['salesid']; ?>"
                                  data-name="<?php echo $row['recipient_name']; ?>"
                                  data-address="<?php echo $row['address']; ?>"
                                  data-address2="<?php echo $row['address2']; ?>"
                                  data-address3="<?php echo $row['address3']; ?>"
                                  data-contact="<?php echo $row['phone']; ?>">
                              <i class='fa fa-search'></i> View
                          </button>
                          <?php if ($user_type != 1) { ?>
                            <?php if ($row['status'] == 0) { ?>
                                <button 
                                onclick="printReceipt('<?php echo $row['salesid']; ?>')" 
                                    class="btn btn-warning btn-sm btn-flat" 
                                    style="background: linear-gradient(to right, #FFA502, #FF7E5F); color: #000; border-radius: 8px;">
                                    <i class="fa fa-print"></i> Print Receipt
                                </button>
                            <?php } ?>
                            
                            <?php if ($row['status'] == 0) { ?>
                                <a href="accept_order?sale_id=<?php echo $row['salesid']; ?>" class="btn btn-sm btn-flat" style="background: linear-gradient(to right, #39FF14, #B4EC51); color: #000; border-radius: 8px;">Packaging Order</a>
                            <?php } ?>
                            
                            <?php if ($row['status'] == 1) { ?>
                                <a href="deliver_order?sale_id=<?php echo $row['salesid']; ?>" class="btn btn-primary btn-sm btn-flat" style="background: linear-gradient(to right, #0072ff, #00c6ff); color: #fff; border-radius: 8px;">Pickup Order</a>
                            <?php } ?>
                            
                            <?php if ($row['status'] == 2) { ?>
                              <?php
                            // Check if rider exists for this sale
                            $rider_check = $conn->prepare("SELECT id FROM rider WHERE sales_id = :sales_id");
                            $rider_check->execute(['sales_id' => $row['salesid']]);
                            $has_rider = $rider_check->fetch() ? true : false;
                            ?>
                            <button 
                                onclick="handleDeliverOrder(<?php echo $row['salesid']; ?>, <?php echo $has_rider ? 'true' : 'false'; ?>)"
                                class="btn btn-warning btn-sm btn-flat deliver-order-btn" 
                                data-has-rider="<?php echo $has_rider ? 'true' : 'false'; ?>"
                                data-sale-id="<?php echo $row['salesid']; ?>"
                                style="background: linear-gradient(to right, #FFAA00, #FF4E00); color: #fff; border-radius: 8px;"
                                <?php echo !$has_rider ? 'disabled' : ''; ?>>
                                Deliver Order
                            </button>
                            <?php } ?>
                            <?php if ($row['status'] == 2) { ?>
                                <button type="button" class="btn btn-info btn-sm btn-flat" style="border-radius: 8px;" data-toggle="modal" data-target="#riderModal" onclick="fetchRiderData(<?php echo $row['salesid']; ?>)">
                                    <i class="fa fa-motorcycle"></i> Assign Rider
                                </button>
                            <?php } ?>

                            <?php if ($row['status'] == 3) { ?>
                                <a href="receive?sale_id=<?php echo $row['salesid']; ?>" class="btn btn-warning btn-sm btn-flat" style="background: linear-gradient(to right, #FFAA00, #FF4E00); color: #fff; border-radius: 8px;">Order Received</a>
                            <?php } ?>
                        <?php } ?>
                            </td>


                          </tr>

                      <?php }
                    }
                    catch(PDOException $e){
                      echo $e->getMessage();
                    }

                    $pdo->close();
                  ?>
                </tbody>
              </table>
            </div>
          </div>
          </div>
        </div>
      </div>
    </section>
     
  </div>
  	<?php include 'includes/footer.php'; ?>
    <!-- <?php include 'includes/walk-in_modal.php'; ?> -->
    <?php include '../includes/profile_modal3.php'; ?>
<!-- Rider Assignment Modal -->
<div id="riderModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="assign_rider">
                <div class="modal-header">
                    <h4 class="modal-title">Assign Rider</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="salesId" name="sales_id">
                    <div class="form-group">
                        <label for="riderName">Rider Name</label>
                        <input type="text" id="riderName" name="rider_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="riderPhone">Phone Number</label>
                        <input type="tel" 
                               id="riderPhone" 
                               name="rider_phone" 
                               class="form-control" 
                               pattern="[0-9]*"
                               maxlength="11"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                               onkeypress="return isNumberKey(event)"
                               required>
                        <small class="text-muted">Numbers only (max 11 digits)</small>
                    </div>
                    <div class="form-group">
                        <label for="riderAddress">Rider Address</label>
                        <input type="text" id="riderAddress" name="rider_address" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Confirm Rider Assignment</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>
<!-- ./wrapper -->
<style>
.deliver-order-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.text-danger {
    margin-top: 5px;
}
</style>
 <script>
  function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
function handleDeliverOrder(saleId, hasRider) {
    if (!hasRider) {
        alert('Please assign a rider first before delivering the order.');
        return false;
    }
    
    // If rider exists, proceed with the order delivery
    window.location.href = `order_receive?sale_id=${saleId}`;
}

// Update the existing code that handles button states
$(document).ready(function() {
    // Prevent default click action on disabled buttons
    $('.deliver-order-btn').on('click', function(e) {
        if ($(this).prop('disabled') || $(this).data('has-rider') === false) {
            e.preventDefault();
            e.stopPropagation();
            alert('Please assign a rider first before delivering the order.');
            return false;
        }
    });
})
// Function to update deliver button states
function updateDeliverButtonStates() {
    $('.deliver-order-btn').each(function() {
        const hasRider = $(this).data('has-rider') === true;
        $(this).prop('disabled', !hasRider);
        const warningText = $(this).next('.text-danger');
        if (hasRider) {
            warningText.hide();
        } else {
            warningText.show();
        }
    });
}

// Call on page load
$(document).ready(function() {
    updateDeliverButtonStates();
});

// Update button states after rider assignment
$('#riderModal').on('hidden.bs.modal', function() {
    location.reload(); // Refresh the page to update button states
});

// Modify the existing fetchRiderData function
function fetchRiderData(salesId) {
    document.getElementById('salesId').value = salesId;
    
    $.ajax({
        url: 'fetch_rider',
        method: 'POST',
        data: { sales_id: salesId },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                document.getElementById('riderName').value = response.data.rider_name;
                document.getElementById('riderPhone').value = response.data.phone_number;
                document.getElementById('riderAddress').value = response.data.rider_address;
                
                // Update the corresponding deliver button
                $(`.deliver-order-btn[data-sale-id="${salesId}"]`).data('has-rider', true);
                updateDeliverButtonStates();
            } else {
                document.getElementById('riderName').value = '';
                document.getElementById('riderPhone').value = '';
                document.getElementById('riderAddress').value = '';
                
                // Update the corresponding deliver button
                $(`.deliver-order-btn[data-sale-id="${salesId}"]`).data('has-rider', false);
                updateDeliverButtonStates();
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax Error:', error);
            alert('Failed to fetch rider details. Please try again.');
        }
    });
}

// Add event listeners when document is ready
$(document).ready(function() {
    // Add input event listener for phone field
    $('#riderPhone').on('input', function() {
        // Remove any non-numeric characters
        $(this).val($(this).val().replace(/\D/g, ''));
        
        // Limit to 11 digits
        if ($(this).val().length > 11) {
            $(this).val($(this).val().slice(0, 11));
        }
    });

    // Form validation before submit
    $('#riderForm').on('submit', function(e) {
        const phoneNumber = $('#riderPhone').val();
        if (phoneNumber.length < 11) {
            e.preventDefault();
            alert('Please enter a valid 11-digit phone number.');
            return false;
        }
    });
});
 </script>
<script>

function printReceipt(saleId) {
    $.ajax({
        url: 'customer_reciept',  
        method: 'GET',
        data: {sale_id: saleId},
        success: function(response) {
            // Create an iframe element for printing
            var printFrame = document.createElement('iframe');
            printFrame.style.position = 'fixed';
            printFrame.style.width = '0px';
            printFrame.style.height = '0px';
            printFrame.style.border = 'none';
            document.body.appendChild(printFrame);

            // Write the receipt content into the iframe
            var printDoc = printFrame.contentWindow.document;
            printDoc.open();
            printDoc.write(`
                <html>
                <head><title>Receipt</title></head>
                <body>
                    ${response} <!-- Include the fetched receipt content -->
                </body>
                </html>
            `);
            printDoc.close();

            // Trigger the print dialog
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();

            // Remove the iframe after printing
            setTimeout(function() {
                document.body.removeChild(printFrame);
            }, 1000);
        },
        error: function() {
            alert('Failed to fetch receipt. Please try again.');
        }
    });
}

document.getElementById('print-report-btn').addEventListener('click', function() {
    var dateRange = document.getElementById('reservation').value;
    if (dateRange) {
        var ex = dateRange.split(' - ');
        var from = new Date(ex[0]);
        var to = new Date(ex[1]);
        $.ajax({
            url: 'fetch_sales_data',
            method: 'POST',
            data: {from: from.toISOString().split('T')[0], to: to.toISOString().split('T')[0]},
            success: function(response) {
                var printFrame = document.createElement('iframe');
                printFrame.style.position = 'fixed';
                printFrame.style.width = '0px';
                printFrame.style.height = '0px';
                printFrame.style.border = 'none';
                document.body.appendChild(printFrame);
                var printDoc = printFrame.contentWindow.document;
                printDoc.open();
                printDoc.write(`
                    <html>
                    <head><title>Sales Report</title></head>
                    <body>
                            ${response}
                        
                    </body>
                    </html>
                `);
                printDoc.close();

                printFrame.contentWindow.focus();
                printFrame.contentWindow.print();
                setTimeout(function() {
                    document.body.removeChild(printFrame);
                }, 1000);
            }
        });
    } else {
        alert('Please select a date range.');
    }
});
</script>
<style>
   .content-wrapper {
      background: white;

   }
   .table-bordered {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
   }
   .modal-content {
    border-radius: 10px;
   }
</style>

<?php include 'includes/scripts.php'; ?>
<!-- Date Picker -->
<script>
$(function(){
  //Date picker
  $('#datepicker_add').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  })
  $('#datepicker_edit').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  })

  //Timepicker
  $('.timepicker').timepicker({
    showInputs: false
  })

  //Date range picker
  $('#reservation').daterangepicker()
  //Date range picker with time picker
  $('#reservationtime').daterangepicker({ timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A' })
  //Date range as a button
  $('#daterange-btn').daterangepicker(
    {
      ranges   : {
        'Today'       : [moment(), moment()],
        'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month'  : [moment().startOf('month'), moment().endOf('month')],
        'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      startDate: moment().subtract(29, 'days'),
      endDate  : moment()
    },
    function (start, end) {
      $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
    }
  )
  
});
$(document).on('click', '.delete-sales', function(e){
    e.preventDefault();
    if(confirm("Are you sure you want to delete this sales")){
        var id = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: 'delete_sales',
            data: {id:id},
            success:function(response){
                location.reload(); 
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText); // Log any errors to the console
            }
        });
    }
});
</script>
<script>
$(function(){
  $(document).on('click', '.transact', function(e){
    e.preventDefault();
    $('#transaction').modal('show');
    var id = $(this).data('id');
    var name = $(this).data('name');
    var address = $(this).data('address');
    var address2 = $(this).data('address2');
    var address3 = $(this).data('address3');
    var contact = $(this).data('contact');
    
    $('#name').text(name);
    $('#address').text(address);
    $('#address2').text(address2);
    $('#address3').text(address3);
    $('#contact_info').text(contact);

    $.ajax({
      type: 'POST',
      url: 'transact',
      data: {id: id},
      dataType: 'json',
      success: function(response){
        $('#date').html(response.date);
        $('#transid').html(response.transaction);
        $('#detail').prepend(response.list);
        $('#total').html(response.total);
        
        // Update rider information
        $('#rider_name').text(response.rider_name);
        $('#phone_number').text(response.phone_number);
        $('#rider_address').text(response.rider_address);
      }
    });
  });

  $("#transaction").on("hidden.bs.modal", function () {
    $('.prepend_items').remove();
  });
});
</script>

</body>
</html>
