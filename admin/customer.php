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
        Customer Users
      </h1>
      <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Customer</li>
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
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat" style="background: linear-gradient(to right, #0072ff, #00c6ff); color: #fff; border-radius: 8px;"><i class="fa fa-plus"></i> Add Customer</a>
            </div>
            <div class="box-body table-responsive">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th class="hidden"></th>
                  <th>Photo</th>
                  <th>Email</th>
                  <th>Name</th>
                  <th>Contact</th>
                  <th>Login Attempts</th>
                  <th>Address</th>
                  <th>Status</th>
                  <th>Date Added</th>
                  <th>Action</th>
                </thead>
                <tbody>
                <?php
                    $conn = $pdo->open();

                    try{
                      // Get user type and ID
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

                      // Different queries based on user type
                      if ($user_type == 1) {
                        // Admin sees all customers
                        $stmt = $conn->prepare("SELECT DISTINCT u.* FROM users u 
                                             WHERE u.type = 0 
                                             ORDER BY u.id DESC");
                        $stmt->execute();
                      } else {
                        // Vendors see only customers who purchased their products
                        $stmt = $conn->prepare("SELECT DISTINCT u.* FROM users u 
                                             INNER JOIN sales s ON s.user_id = u.id 
                                             INNER JOIN details d ON d.sales_id = s.id 
                                             INNER JOIN products p ON p.id = d.product_id 
                                             WHERE u.type = 0 
                                             AND s.admin_id = :admin_id 
                                             ORDER BY u.id DESC");
                        $stmt->execute(['admin_id' => $user_id]);
                      }

                      foreach($stmt as $row){
                        $image = (!empty($row['photo'])) ? '../images/'.$row['photo'] : '../images/profile.jpg';
                        $status = ($row['status']) ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Deactive</span>';
                        $active = (!$row['status']) ? '<span class="pull-right"><a href="#activate" class="status" data-toggle="modal" data-id="'.$row['id'].'"><i class="fa fa-check-square-o"></i></a></span>' : '<span class="pull-right"><a href="#deactivate" class="status" data-toggle="modal" data-id="'.$row['id'].'"><i class="fa fa-check-square-o"></i></a></span>';
                        echo "
                          <tr>
                            <td class='hidden'></td>
                            <td>
                              <img class='pic' src='".$image."'>
                              <img class='picbig' src='".$image."'>
                              <span class='pull-right'><a href='#edit_photo' class='photo' data-toggle='modal' data-id='".$row['id']."'><i class='fa fa-edit'></i></a></span>
                            </td>
                            <td>".$row['email']."</td>
                            <td>".$row['firstname'].' '.$row['lastname']."</td>
                            <td>".$row['contact_info']."</td>
                            <td>".$row['login_attempts']."</td>
                            <td>
                            <button class='btn btn-info btn-sm view btn-flat' style='color: #fff; border-radius: 8px;' 
                            data-id='".$row['id']."'><i class='fa fa-eye'></i> View</button>
                            </td>
                            <td>
                              ".$status."
                              ".$active."
                            </td>
                            <td>".date('M d, Y', strtotime($row['created_on']))."</td>
                            <td>
                              <button class='btn btn-success btn-sm edit btn-flat' style='color: #fff; border-radius: 8px;' data-id='".$row['id']."'><i class='fa fa-edit'></i> Edit</button>
                              <button class='btn btn-danger btn-sm delete btn-flat' style='color: #fff; border-radius: 8px;' data-id='".$row['id']."'><i class='fa fa-trash'></i> Delete</button>
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
    <?php include 'includes/customer_modal.php'; ?>


<!-- ./wrapper -->

<?php include 'includes/scripts.php'; ?>
<script>
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

  $(document).on('click', '.status', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

});

function fetchAddress(lat, lng) {
    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;

    // Show loading text while fetching
    $('#fetched_address').text('Fetching...');

    $.getJSON(url, function(data) {
        if (data && data.display_name) {
            // Update the new fetched address field
            $('#fetched_address').text(data.display_name);
        } else {
            $('#fetched_address').text('Unable to fetch address');
        }
    }).fail(function() {
        $('#fetched_address').text('Error retrieving address');
    });
}



function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'users_row',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.userid').val(response.id);
      $('#edit_email').val(response.email);
      $('#edit_password').val(response.password);
      $('#edit_firstname').val(response.firstname);
      $('#edit_lastname').val(response.lastname);
      $('#edit_address').val(response.address);
      $('#edit_contact').val(response.contact_info);
      $('.fullname').html(response.firstname+' '+response.lastname);
      
      // New code for view modal
      $.ajax({
        type: 'POST',
        url: 'users_location',  // You'll need to create this endpoint
        data: {id:id},
        dataType: 'json',
        success: function(locationData){
          // Update view modal with additional details
          $('#view_photo').attr('src', '../images/' + (response.photo || 'profile.jpg'));
          $('#view_fullname').text(response.firstname + ' ' + response.lastname);
          $('#view_email').text(response.email);
          $('#view_contact').text(response.contact_info);
          $('#view_address').text(response.address);
          $('#view_address2').text(response.address2 || 'N/A');
          
          if (locationData.latitude && locationData.longitude) {
    $('#view_latitude').text(locationData.latitude);
    $('#view_longitude').text(locationData.longitude);
    $('#location_trace_btn').show();

    // Fetch and display the fetched address
    fetchAddress(locationData.latitude, locationData.longitude);

    // Store location data for tracing
    window.currentUserLocation = {
        latitude: locationData.latitude,
        longitude: locationData.longitude
    };
} else {
    $('#view_latitude').text('N/A');
    $('#view_longitude').text('N/A');
    $('#fetched_address').text('N/A');
    $('#location_trace_btn').hide();
    window.currentUserLocation = null;
}

        },
        error: function() {
          $('#view_latitude').text('N/A');
          $('#view_longitude').text('N/A');
          $('#location_trace_btn').hide();
          window.currentUserLocation = null;
        }
      });
    }
  });
}

// Add function to trace user location
function traceUserLocation() {
  if (window.currentUserLocation) {
    // Open Google Maps with the location
    var url = `https://www.google.com/maps?q=${window.currentUserLocation.latitude},${window.currentUserLocation.longitude}`;
    window.open(url, '_blank');
  } else {
    swal({
      title: 'Location Unavailable',
      text: 'No location data found for this user.',
      icon: 'warning',
      button: 'OK'
    });
  }
}

// Add a click event for the view button
$(document).on('click', '.view', function(e){
  e.preventDefault();
  $('#view').modal('show');
  var id = $(this).data('id');
  getRow(id);
});
</script>
</body>
</html>
