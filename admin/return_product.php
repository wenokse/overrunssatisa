<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<?php
 $user_type = $_SESSION['admin'];
 $user_id = $_SESSION['admin'];
?>
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
        <li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li>
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
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <!-- <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> Add Customer</a> -->
              <div class="pull-right">
                
                
              </div>
            </div>
            <div class="box-body table-responsive">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th class="hidden"></th>
                  <th>Date</th>
                  <th>Buyer Name</th>
                  <th>Transaction #</th>
                  <th>Amount</th>
                  <th>Full Details</th>
                  <?php if($user_type != 195): ?>
                  <th>Action</th>
                  <?php endif; ?>
                </thead>
                <tbody>
                <?php
                    $conn = $pdo->open();

                    try{
                      $user_type = $_SESSION['admin'];
                      $user_id = $_SESSION['admin'];

                      if($user_type == 195){
                        $stmt = $conn->prepare("SELECT DISTINCT r.*, r.id AS return_id, 
                                             u.firstname, u.lastname 
                                             FROM return_products r 
                                             LEFT JOIN users u ON u.id=r.user_id 
                                             ORDER BY r.return_date DESC");
                        $stmt->execute();
                      } else {
                        $stmt = $conn->prepare("SELECT DISTINCT r.*, r.id AS return_id, 
                                             u.firstname, u.lastname 
                                             FROM return_products r 
                                             LEFT JOIN users u ON u.id=r.user_id 
                                             LEFT JOIN details d ON d.sales_id=r.sales_id 
                                             LEFT JOIN products p ON p.id=d.product_id 
                                             WHERE p.user_id = :vendor_id 
                                             ORDER BY r.return_date DESC");
                        $stmt->execute(['vendor_id' => $user_id]);
                      }

                      foreach($stmt as $row){
                        $stmt2 = $conn->prepare("SELECT d.*, p.price 
                                               FROM details d 
                                               LEFT JOIN products p ON p.id=d.product_id 
                                               WHERE d.sales_id=:id");
                        $stmt2->execute(['id' => $row['sales_id']]);
                        
                        $total = 0;
                        foreach($stmt2 as $details){
                          $subtotal = $details['price'] * $details['quantity'] + $details['shipping'];
                          $total += $subtotal;
                        }
                        ?>
                        <tr>
                          <td class='hidden'></td>
                          <td><?php echo date('M d, Y', strtotime($row['return_date']))?></td>
                          <td><?php echo $row['firstname'].' '.$row['lastname']?></td>
                          <td><?php echo $row['pay_id']?></td>
                          <td>&#8369; <?php echo number_format($total, 2)?></td>
                          <td>
                            <button type='button' class='btn btn-info btn-sm btn-flat transac' 
                                    style='background: linear-gradient(to right, #00C9FF, #92FE9D); 
                                           color: #fff; border-radius: 8px;' 
                                    data-id="<?php echo $row['sales_id']?>">
                              <i class='fa fa-search'></i> View
                            </button>
                          </td>
                          <?php if($user_type != 195): ?>
                          <td>
                            <button class='btn btn-sm btn-flat btn-danger delete-return' 
                                    style='color: #fff; border-radius: 8px;' 
                                    data-id="<?php echo $row['return_id']?>">
                              <i class='fa fa-trash'></i> Delete
                            </button>
                          </td>
                          <?php endif; ?>
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
                url: 'delete_return',
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
      url: 'transac',
      data: {id:id},
      dataType: 'json',
      success:function(response){
        $('#date').html(response.date);
        $('#transid').html(response.transaction);
        $('#detail').prepend(response.list);
        $('#total').html(response.total);
        $('#address_details').html(response.address);
        $('#rider_details').html(response.rider);
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
