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
        Comments
      </h1>
      <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Comments</li>
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
              <div class="pull-right">
                
              </div>
            </div>
            <div class="box-body table-responsive">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th class="hidden"></th>
                  <th>Reported by</th>
                  <th>Vendor</th>
                  <th>Status</th>
                  <th>View</th>
                  <th>Action</th>
                </thead>
                <tbody>
  <?php
    $conn = $pdo->open();
    try {
      $stmt = $conn->prepare("SELECT r.*, 
                             reporter.firstname as reporter_name, 
                             reporter.lastname as reporter_lastname,
                             vendor.store as store_name 
                             FROM reports r 
                             LEFT JOIN users reporter ON r.reporter_id = reporter.id 
                             LEFT JOIN users vendor ON r.shop_id = vendor.id AND vendor.type = 2 
                             ORDER BY r.created_at DESC");
      $stmt->execute();
      foreach($stmt as $row){
        $status_class = '';
        switch($row['status']) {
          case 'pending':
            $status_class = 'label-warning';
            break;
          case 'reviewed':
            $status_class = 'label-info';
            break;
          case 'resolved':
            $status_class = 'label-success';
            break;
          case 'dismissed':
            $status_class = 'label-danger';
            break;
        }
        echo "
          <tr>
            <td class='hidden'></td>
            <td>".$row['reporter_name'].' '.$row['reporter_lastname']."</td>
            <td>".$row['store_name']."</td>
            <td><span class='label ".$status_class."'>".ucfirst($row['status'])."</span></td>
            <td>
              <button class='btn btn-info btn-sm btn-flat view' data-id='".$row['id']."' data-reason='".htmlspecialchars($row['reason'], ENT_QUOTES)."' 
                      data-description='".htmlspecialchars($row['description'], ENT_QUOTES)."' data-status='".$row['status']."' 
                      data-date='".$row['created_at']."'>
                <i class='fa fa-eye'></i> View
              </button>
            </td>
            <td>
              <button class='btn btn-danger btn-sm btn-flat delete' data-id='".$row['id']."'>
                <i class='fa fa-trash'></i> Delete
              </button>
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
 <!-- View Report Modal -->
<div class="modal fade" id="viewReport" tabindex="-1" aria-labelledby="viewReportLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content custom-modal">

      <!-- Modal Header -->
      <div class="modal-header bg-primary text-white rounded-top">
        <h5 class="modal-title" id="viewReportLabel"><b>Report Details</b></h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <div class="form-group row">
          <label class="col-sm-3 col-form-label"><strong>Reason:</strong></label>
          <div class="col-sm-9">
            <p id="view-reason" class="form-control-static">-</p>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label"><strong>Description:</strong></label>
          <div class="col-sm-9">
            <p id="view-description" class="form-control-static">-</p>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label"><strong>Status:</strong></label>
          <div class="col-sm-9">
            <p id="view-status" class="form-control-static">-</p>
          </div>
        </div>
        <div class="form-group row">
        <label class="col-sm-3 col-form-label"><strong>Status:</strong></label>
        <div class="col-sm-9">
              <select class="form-control" id="status" name="status">
                <option value="pending">Pending</option>
                <option value="reviewed">Reviewed</option>
                <option value="resolved">Resolved</option>
                <option value="dismissed">Dismissed</option>
              </select>
        </div>
            </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label"><strong>Reported On:</strong></label>
          <div class="col-sm-9">
            <p id="view-date" class="form-control-static">-</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">Close</button>
          <input type="hidden" id="report_id" name="report_id">
          <button type="button" class="btn btn-success btn-flat" id="updateStatus">Update Status</button>
        </div>
    </div>
  </div>
</div>
  <?php include 'includes/footer.php'; ?>

<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
  $(document).on('click', '.view', function(e){
    e.preventDefault();
    $('#viewReport').modal('show');

    // Get data from button attributes
    var report_id = $(this).data('id');
    var reason = $(this).data('reason');
    var description = $(this).data('description');
    var status = $(this).data('status');
    var date = $(this).data('date');

    // Populate the modal fields
    $('#report_id').val(report_id); // Set report_id in hidden input
    $('#view-reason').text(reason);
    $('#view-description').text(description);
    $('#view-status').text(status.charAt(0).toUpperCase() + status.slice(1));
    $('#view-date').text(date);
  });

  $('#updateStatus').click(function(e){
    e.preventDefault();

    // Retrieve report ID from hidden input
    var report_id = $('#report_id').val();
    var status = $('#status').val();

    swal({
      title: "Are you sure?",
      text: "Do you want to update the status of this report?",
      icon: "warning",
      buttons: true,
      dangerMode: false,
    })
    .then((willUpdate) => {
      if (willUpdate) {
        $.ajax({
          type: 'POST',
          url: 'report_status',
          data: {
            report_id: report_id,
            status: status
          },
          dataType: 'json',
          success: function(response){
            if(response.status == 'success'){
              $('#viewReport').modal('hide');
              swal("Success!", response.message, "success").then(() => {
                location.reload();
              });
            } else {
              swal("Error!", response.message, "error");
            }
          }
        });
      }
    });
  });
});

</script>

<style>
  .custom-modal {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
  }

  .modal-header.rounded-top {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
  }

  .modal-footer {
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
  }

  .btn-secondary {
    border-radius: 5px;
  }
</style>

</body>
</html>