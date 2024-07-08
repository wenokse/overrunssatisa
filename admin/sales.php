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
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
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
                <form method="POST" class="form-inline" action="sales_print.php">
                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right col-sm-8" id="reservation" name="date_range">
                  </div>
                  <button type="submit" class="btn btn-success btn-sm btn-flat" name="print"><span class="glyphicon glyphicon-print"></span>&nbsp;Report Print</button>
                  <!--<form method="POST" action="customer_reciept.php">
                    <button type="submit" class="btn btn-success btn-sm btn-flat" name="customer_reciept.php">
                        <span class="glyphicon glyphicon-print"></span>&nbsp;Customer Receipt
                    </button>
                </form>-->
                </form>
                
              </div>
            </div>
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

                    try{
                      $stmt = $conn->prepare("SELECT *, sales.id AS salesid, sales.status FROM sales LEFT JOIN users ON users.id=sales.user_id ORDER BY   sales.sales_date DESC");
                      $stmt->execute();
                      foreach($stmt as $row){
                        $stmt = $conn->prepare("SELECT * FROM details LEFT JOIN products ON products.id=details.product_id WHERE details.sales_id=:id");
                        $stmt->execute(['id'=>$row['salesid']]);
                        $total = 0;
                        foreach($stmt as $details){
                          $subtotal = $details['price']*$details['quantity']+$details['shipping'];
                          $total += $subtotal;
                        } ?>

                          <tr>
                            <td class='hidden'></td>
                            <td><?php echo date('M d, Y', strtotime($row['sales_date']))?></td>
                            <td><?php echo $row['firstname'].' '.$row['lastname']?></td>
                            <td><?php echo$row['pay_id']?></td>
                            <td>&#8369; <?php echo number_format($total, 2)?></td>
                            <td>
                              <?php if ($row['status'] == 0) { ?>
                                  <p class="btn-sm btn-warning text-center" style='border-radius: 8px;'>Pending</p>
                                <?php } ?>
                                <?php if ($row['status'] == 1) { ?>
                                  <p class="btn-sm btn-info text-center" style='border-radius: 8px;'>Accepted</p>
                                <?php } ?>
                                <?php if ($row['status'] == 2) { ?>
                                  <p class="btn-sm btn-primary text-center" style='border-radius: 8px;'>Delivered</p>
                                <?php } ?>
                                <?php if ($row['status'] == 3) { ?>
                                  <p class="btn-sm btn-success text-center" style='border-radius: 8px;'>Received</p>
                                <?php } ?>

                            </td>
                            <td>
                            <button type='button' class='btn btn-info btn-sm btn-flat transact' style='border-radius: 8px;'
                                    data-id="<?php echo $row['salesid']; ?>"
                                    data-name="<?php echo $row['firstname'].' '.$row['lastname']; ?>"
                                    data-address="<?php echo $row['address']; ?>"
                                    data-address2="<?php echo $row['address2']; ?>"
                                    data-contact="<?php echo $row['contact_info']; ?>">
                              <i class='fa fa-search'></i> View
                            </button>
                            <?php if ($row['status'] == 0) { ?>
                                  <a href="accept_order.php?sale_id=<?php  echo$row['salesid'] ?>"class='btn btn-success btn-sm btn-flat' style='border-radius: 8px;'>Accept Order</a>
                                <?php } ?>
                                <?php if ($row['status'] == 0) { ?>
                                      <a href="customer_reciept.php?sale_id=<?php echo $row['salesid']; ?>" class='btn btn-warning btn-sm btn-flat-print' style='border-radius: 8px;'><i class='fa fa-print'></i> Print Receipt</a>
                                  <?php } ?>
                                <?php if ($row['status'] == 1) { ?>
                                  <a href="deliver_order.php?sale_id=<?php  echo$row['salesid'] ?>"class='btn btn-primary btn-sm btn-flat' style='border-radius: 8px;'>Deliver Order</a>
                                <?php } ?>
                                <?php if ($row['status'] == 2) { ?>
                                  <a href="order_receive.php?sale_id=<?php  echo$row['salesid'] ?>"class='btn btn-warning btn-sm btn-flat' style='border-radius: 8px;'>Order Received</a>
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
    </section>
     
  </div>
  	<?php include 'includes/footer.php'; ?>
    <!-- <?php include 'includes/walk-in_modal.php'; ?> -->
    <?php include '../includes/profile_modal3.php'; ?>

</div>
<!-- ./wrapper -->
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
            url: 'delete_sales.php',
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
    var contact = $(this).data('contact');
    
    $('#name').text(name);
    $('#address').text(address);
    $('#address2').text(address2);
    $('#contact_info').text(contact);

    $.ajax({
      type: 'POST',
      url: 'transact.php',
      data: {id: id},
      dataType: 'json',
      success: function(response){
        $('#date').html(response.date);
        $('#transid').html(response.transaction);
        $('#detail').prepend(response.list);
        $('#total').html(response.total);
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
