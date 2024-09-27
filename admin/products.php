<?php include 'includes/session.php'; ?>

<?php
  $where = '';
  $params = array();
  if(isset($_GET['category'])){
    $catid = $_GET['category'];
    $where = 'WHERE category_id = :catid';
    $params[':catid'] = $catid;
  }
 
?>
<?php
 $user_type = $_SESSION['admin'];
 $user_id = $_SESSION['admin'];
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Product List
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <!-- <li>Products</li> -->
        <li class="active">Product</li>
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
            <?php if ($user_type != 195): // Assuming 1 is the admin type ?>
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat" style="background: linear-gradient(to right, #0072ff, #00c6ff); color: #fff; border-radius: 8px;" id="addproduct"><i class="fa fa-plus"></i> Add Product</a>
            <?php endif; ?>
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

                        foreach($stmt as $crow){
                          $selected = ($crow['id'] == $catid) ? 'selected' : ''; 
                          echo "
                            <option value='".$crow['id']."' ".$selected.">".$crow['name']."</option>
                          ";
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
                  <th class="hidden"></th>
                  <th>Product Name</th>
                  <th>Photo</th>
                  <th>Price</th>
                  <th>Views Today</th>
                  <th>Action</th>
                </thead>
                <tbody>
                <?php
      $conn = $pdo->open();

      try {
        $now = date('Y-m-d');
        if ($user_type == 195) {
            $stmt = $conn->prepare("SELECT * FROM products $where ORDER BY id DESC");
        } else {
            if ($where) {
                $where = "WHERE user_id = :user_id AND " . ltrim($where, "WHERE ");
            } else {
                $where = "WHERE user_id = :user_id";
            }
            $stmt = $conn->prepare("SELECT * FROM products $where ORDER BY id DESC");
            $params[':user_id'] = $user_id;
        }
        
        $stmt->execute($params);
        
        foreach($stmt as $row){
                        $image = (!empty($row['photo'])) ? '../images/'.$row['photo'] : '../images/noimage.jpg';
                        $counter = ($row['date_view'] == $now) ? $row['counter'] : 0;
                        echo "
                          <tr>
                            <td class='hidden'></td>
                            <td>".$row['name']."</td>
                            <td>
                              <img class='pic' src='".$image."'>
                              <img class='picbig' src='".$image."'>
                               ";
                              if ($user_type != 195) {
                                echo "
                              <span class='pull-right'><a href='#edit_photo' class='photo' data-toggle='modal' data-id='".$row['id']."'><i class='fa fa-edit'></i></a></span>
                             ";
                              }
                        echo "
                              </td>
                            <td>&#8369; ".number_format($row['price'], 2)."</td>
                            <td>".$counter."</td>
                           <td>
                              <a href='#description' data-toggle='modal' class='btn btn-info btn-sm btn-flat desc' style='background: linear-gradient(to right, #00C9FF, #92FE9D); color: #fff; border-radius: 8px;' data-id='".$row['id']."'><i class='fa fa-search'></i> View</a>
                              ";
                              if ($user_type != 195) {
                                echo "
                                  <button class='btn btn-success btn-sm edit btn-flat' style='background: linear-gradient(to right, #39FF14, #B4EC51); color: #fff; border-radius: 8px;'  data-id='".$row['id']."'><i class='fa fa-edit'></i> Edit</button>
                                  <button class='btn btn-danger btn-sm delete btn-flat' style='background: linear-gradient(to right, #FF416C, #FF4B2B); color: #fff; border-radius: 8px;' data-id='".$row['id']."'><i class='fa fa-trash'></i> Delete</button>
                                ";
                              }
                        echo "
                            </td>
                          </tr>
                        ";
                      }
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
  
</div>
  	<?php include 'includes/footer.php'; ?>
    <?php include 'includes/products_modal.php'; ?>
    <?php include 'includes/products_modal2.php'; ?>

<!-- ./wrapper -->

<?php include 'includes/scripts.php'; ?>
<script>


$(document).ready(function() {
  let colorCount = 1;
  
  $('#add-color').click(function() {
    colorCount++;
    const newColorField = `
      <div class="form-group color-field">
        <label for="color${colorCount}" class="col-sm-1 control-label">Color</label>
        <div class="col-sm-5">
          <input type="text" class="form-control" name="colors[]" required>
        </div>
        <label for="color_photo${colorCount}" class="col-sm-1 control-label">Photo</label>
        <div class="col-sm-5">
          <input type="file" name="color_photos[]" required>
        </div>
      </div>
    `;
    $('#color-fields').append(newColorField);
  });
});

$(function(){
  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.delete', function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.photo', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.desc', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

  $('#select_category').change(function(){
    var val = $(this).val();
    if(val == 0){
      window.location = 'products.php';
    }
    else{
      window.location = 'products.php?category='+val;
    }
  });

  $('#addproduct').click(function(e){
    e.preventDefault();
    getCategory();
  });

  $("#addnew").on("hidden.bs.modal", function () {
      $('.append_items').remove();
  });

  $("#edit").on("hidden.bs.modal", function () {
      $('.append_items').remove();
  });

});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'products_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.prodid').val(response.prodid);
      $('#desc').html(response.description);
      $('.name').html(response.prodname);
      $('#edit_name').val(response.prodname);
      $('#catselected').val(response.category_id).html(response.catname);
      $('#availselected').val(response.prodid).html(response.prodavail);
      $('#edit_price').val(response.price);
      $('#edit_stock').val(response.stock);
      CKEDITOR.instances["editor2"].setData(response.description);
      
      // Clear existing color fields
      $('#edit_color-fields').empty();
      
      // Add existing colors
      if(response.colors && response.colors.length > 0){
        response.colors.forEach(function(color, index){
          addColorField(color.color, color.photo);
        });
      } else {
        addColorField();
      }
      
      getCategory();
    }
  });
}

function addColorField(color = '', photo = ''){
    let colorCount = $('#edit_color-fields .color-field').length + 1;
    let newColorField = `
      <div class="form-group color-field">
        <label for="color${colorCount}" class="col-sm-1 control-label">Color</label>
        <div class="col-sm-5">
          <input type="text" class="form-control" name="edit_colors[]" value="${color}">
        </div>
        <label for="color_photo${colorCount}" class="col-sm-1 control-label">Photo</label>
        <div class="col-sm-5">
          <input type="file" name="edit_color_photos[]">
          ${photo ? `<p>Current: ${photo}</p>` : ''}
          <input type="hidden" name="current_color_photos[]" value="${photo}">
        </div>
      </div>
    `;
    $('#edit_color-fields').append(newColorField);
  }

  // Add event listener for the "Add Color" button
  $(document).on('click', '#add-edit-color', function(e) {
    e.preventDefault();
    addColorField();
  });

function getCategory(){
  $.ajax({
    type: 'POST',
    url: 'category_fetch.php',
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
