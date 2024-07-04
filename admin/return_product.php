<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>
 <script src="../js/sweetalert.min.js"></script>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Return Products
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Return</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
       <?php
  if (isset($_SESSION['error']) || isset($_SESSION['success'])) {
    $message = isset($_SESSION['error']) ? $_SESSION['error'] : $_SESSION['success'];
    $icon = isset($_SESSION['error']) ? 'error' : 'success';
    echo "
      <script>
        swal({
          title: '". $message ."',
          icon: '". $icon ."',
          button: 'OK'
        });
      </script>
    ";
    unset($_SESSION['error']);
    unset($_SESSION['success']);
  }
?>
<style>
   .content-wrapper {
      background: white;
   }
   .modal-content {
    border-radius: 10px;
   }
</style>
      <div class="">
        <div class="col-xs-12">
          <div class="">
            <div class="box-header with-border">
              <!-- <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> Add Customer</a> -->
              <div class="pull-right">
                
                
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
                  <th>Full Details</th>
                  <th>Action</th>
                </thead>
                <tbody>
                  <?php
                    $conn = $pdo->open();

                    try{
                      $stmt = $conn->prepare("SELECT *, return_products.id AS return_id, users.firstname, users.lastname FROM return_products LEFT JOIN users ON users.id=return_products.user_id ORDER BY return_products.return_date DESC");
                      $stmt->execute();
                      foreach($stmt as $row){
                        $stmt = $conn->prepare("SELECT * FROM details LEFT JOIN products ON products.id=details.product_id WHERE details.sales_id=:id");
                        $stmt->execute(['id'=>$row['sales_id']]);
                        $total = 0;
                        foreach($stmt as $details){
                          $subtotal = $details['price']*$details['quantity']+$details['shipping'];
                          $total += $subtotal;
                        } ?>

                          <tr>
                            <td class='hidden'></td>
                            <td><?php echo date('M d, Y', strtotime($row['return_date']))?></td>
                            <td><?php echo $row['firstname'].' '.$row['lastname']?></td>
                            <td><?php echo $row['pay_id']?></td>
                            <td>&#8369; <?php echo number_format($total, 2)?></td>
                            
                            <td>
                            <button type='button' class='btn btn-info btn-sm btn-flat transac' style='border-radius: 8px;'  data-id="<?php echo$row['sales_id']?>"><i class='fa fa-search'></i> View</button>
                            </td>
                            <td>
                            <button class='btn btn-sm btn-flat btn-danger delete-return' style='border-radius: 8px;'  data-id="<?php echo $row['return_id']?>"><i class='fa fa-trash'></i> Delete</button>
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
    
    <?php include '../includes/profile_modal2.php'; ?>

</div>
<!-- ./wrapper -->

<?php include 'includes/scripts.php'; ?>
<!-- Date Picker -->
<script>

$(document).on('click', '.delete-return', function(e){
    e.preventDefault();
     var id = $(this).data('id');
    swal({
        title: "Are you sure you want to delete this return?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                type: 'POST',
                url: 'delete_return.php',
                data: {id: id},
                dataType: 'json',
                success: function(response){
                    if(response.success){
                        swal("Transaction deleted successfully!", {
                            icon: "success",
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        swal("Error deleting transaction!", {
                            icon: "error",
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    });
});
</script>
<script>
$(function(){
  $(document).on('click', '.transac', function(e){
    e.preventDefault();
    $('#transaction').modal('show');
    var id = $(this).data('id');
    $.ajax({
      type: 'POST',
      url: 'transac.php',
      data: {id:id},
      dataType: 'json',
      success:function(response){
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
