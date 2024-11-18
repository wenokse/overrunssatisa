<?php include 'includes/session.php'; ?>
<?php
  $where = '';
  if(isset($_GET['category'])){
    $catid = $_GET['category'];
    $where = 'WHERE category_id ='.$catid;
  }
  $user_type = $_SESSION['admin'];
  $user_id = $_SESSION['admin'];

?>
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
      Inventory List
      </h1>
      <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Inventory</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
    <?php
  if (isset($_SESSION['error']) || isset($_SESSION['success'])) {
    $message = isset($_SESSION['error']) ? $_SESSION['error'] : $_SESSION['success'];
    $icon = isset($_SESSION['error']) ? 'error' : 'success';
    echo "
      <script src='js/sweetalert.min.js'></script>
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
</style>
<div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <div class="pull-right">
                                <form class="form-inline">
                                    <div class="form-group">
                                        <label>Category: </label>
                                        <select class="form-control input-sm" id="select_category">
                                            <option value="0">ALL</option>
                                            <?php
                                            $conn = $pdo->open();
                                            $stmt = $conn->prepare("SELECT * FROM category");
                                            $stmt->execute();
                                            foreach ($stmt as $crow) {
                                                $selected = ($crow['id'] == $catid) ? 'selected' : '';
                                                echo "<option value='" . $crow['id'] . "' " . $selected . ">" . htmlspecialchars($crow['name'], ENT_QUOTES) . "</option>";
                                            }
                                            $pdo->close();
                                            ?>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="box-body table-responsive">
                            <table id="example1" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="hidden"></th>
                                        <th>Product Name</th>
                                        <th>Photo</th>
                                        <th>Stock</th>
                                        <th>Availability</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $conn = $pdo->open();
                                    try {
                                        $now = date('Y-m-d');
                                        if ($user_type == 195) {
                                            $stmt = $conn->prepare("SELECT * FROM products $where ORDER BY id DESC");
                                        } else {
                                            $stmt = $conn->prepare("SELECT * FROM products WHERE user_id = :user_id $where ORDER BY id DESC");
                                            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                                        }
                                        if (isset($catid)) {
                                            $stmt->bindParam(':catid', $catid, PDO::PARAM_INT);
                                        }
                                        $stmt->execute();
                                        foreach ($stmt as $row) {
                                            $image = (!empty($row['photo'])) ? '../images/' . htmlspecialchars($row['photo'], ENT_QUOTES) : '../images/noimage.jpg';
                                            $avail = ($row['stock']) ? '<span class="label label-success">Stock In</span>' : '<span class="label label-danger">Stock Out</span>';
                                            $edit_avail = '';
                                            if ($user_type != 195 && $row['stock']) {
                                                $edit_avail = '<button class="btn btn-success btn-sm btn-flat edit_stockout" style="color: #fff; border-radius: 8px;" data-id="' . $row['id'] . '"><i class="fa fa-edit"></i> Edit</button>';
                                            }
                                            echo "
                                            <tr>
                                                <td class='hidden'></td>
                                                <td>" . htmlspecialchars($row['name'], ENT_QUOTES) . "</td>
                                                <td>
                                                    <img class='pic' src='" . $image . "' alt='Product Image'>
                                                    <img class='picbig' src='" . $image . "' alt='Product Image Large'>
                                                </td>
                                                <td>" . $row['stock'] . "</td>
                                                <td>" . $avail . "</td>
                                                <td>" . $edit_avail . "</td>
                                            </tr>
                                            ";
                                        }
                                    } catch(PDOException $e) {
                                        echo "There is some problem in connection: " . $e->getMessage();
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
</div>
<?php include 'includes/footer.php'; ?>
<?php include 'includes/inventory_modal.php'; ?>

<?php include 'includes/scripts.php'; ?>
<script>
$(function(){

  $(document).on('click', '.edit_stockin', function(e){
    e.preventDefault();
    $('#edit_stockin').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.edit_stockout', function(e){
    e.preventDefault();
    $('#edit_stockout').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $('#select_category').change(function(){
    var val = $(this).val();
    if(val == 0){
      window.location = 'inventory';
    }
    else{
      window.location = 'inventory?category='+val;
    }
  });

});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'products_row',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('#desc').html(response.description);
      $('.name').html(response.prodname);
      $('.prodid').val(response.prodid);
      $('#edit_name').val(response.prodname);
      $('#catselected').val(response.category_id).html(response.catname);
      $('#edit_price').val(response.price);
      CKEDITOR.instances["editor2"].setData(response.description);
      getCategory();
    }
  });
}
function getCategory(){
  $.ajax({
    type: 'POST',
    url: 'category_fetch',
    dataType: 'json',
    success:function(response){
      $('#category').append(response);
      $('#edit_category').append(response);
    }
  });
}
</script>
</body>
</html>
