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
        Vendor List
      </h1>
      <ol class="breadcrumb">
        <li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Vendor</li>
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
   .action-buttons {
        white-space: nowrap;
        min-width: 120px;
      }
      .table-responsive {
        margin: 15px 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }
      #example1 {
        width: 100%;
        margin-bottom: 0;
        white-space: nowrap;
      }
      
      
      .table-bordered > thead > tr > th {
        border-bottom-width: 1px;
      }
</style>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <a href="#addnewvendor" data-toggle="modal" class="btn  btn-sm btn-flat" style="background: linear-gradient(to right, #0072ff, #00c6ff); color: #fff; border-radius: 8px;"><i class="fa fa-plus"></i> Add Vendor</a>
            </div>
            <div class="box-body table-responsive">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th class="hidden"></th>
                  <th>Photo</th>
                  <th>Email</th>
                  <th>Name</th>
                  <th>Name Of Store</th>
                  <th>Valid ID</th>
                  <th>TIN Number</th>
                  <th>Location</th>
                  <th>View</th>
                  <th>Status</th>
                  <th>Date Added</th>
                  <th>Action</th>
                </thead>
                <tbody>
                <?php
                    $conn = $pdo->open();

                    try {
                        $stmt = $conn->prepare("SELECT * FROM users WHERE type=:type ORDER BY id DESC");
                        $stmt->execute(['type' => 2]);
                        
                        foreach ($stmt as $row) {
                            $image = (!empty($row['photo'])) ? '../images/' . $row['photo'] : '../images/profile.jpg';
                            $images = (!empty($row['valid_id'])) ? '../images/' . $row['valid_id'] : '../images/profile.jpg';
                            
                            // Status label handling
                            switch ($row['status']) {
                                case 1:
                                    $status = '<span class="label label-success">Active</span>';
                                    break;
                                case 0:
                                    $status = '<span class="label label-danger">Deactive</span>';
                                    break;
                                case 3:
                                    $status = '<span class="label label-warning">Pending</span>';
                                    break;
                                case 5:
                                    $status = '<span class="label label-danger">Declined</span>';
                                    break;
                            }
                            
                            $active = '';  
                            if ($row['status'] != 5 && $row['status'] != 3) {
                                $active = (!$row['status']) 
                                    ? '<span class="pull-right"><a href="#activate" class="status" data-toggle="modal" data-id="'.$row['id'].'"><i class="fa fa-pencil-square-o"></i></a></span>' 
                                    : '<span class="pull-right"><a href="#deactivate" class="status" data-toggle="modal" data-id="'.$row['id'].'"><i class="fa fa-check-square-o"></i></a></span>';
                            }
                            
                            if ($row['status'] == 3) {
                                $actionButtons = "
                                    <button class='btn btn-success btn-sm accept btn-flat' style='border-radius: 8px;' data-id='{$row['id']}'><i class='fa fa-check'></i> Accept</button>
                                    <button class='btn btn-danger btn-sm decline btn-flat' style='border-radius: 8px;' data-id='{$row['id']}'><i class='fa fa-close'></i> Decline</button>
                                ";
                            } elseif ($row['status'] == 5) {
                                $actionButtons = "
                                    <button class='btn btn-danger btn-sm delete btn-flat' style='color: #fff; border-radius: 8px;' data-id='{$row['id']}'><i class='fa fa-trash'></i> Delete</button>
                                ";
                            } else {
                                $actionButtons = "
                                    <button class='btn btn-success btn-sm edit btn-flat' style='color: #fff; border-radius: 8px;' data-id='{$row['id']}'><i class='fa fa-edit'></i> Edit</button>
                                    <button class='btn btn-danger btn-sm delete btn-flat' style='color: #fff; border-radius: 8px;' data-id='{$row['id']}'><i class='fa fa-trash'></i> Delete</button>
                                ";
                            }

                            echo "
                                <tr>
                                    <td class='hidden'></td>
                                    <td>
                                        <img class='pic' src='{$image}'>
                                        <img class='picbig' src='{$image}'>
                                        <span class='pull-right'><a href='#edit_photo' class='photo' data-toggle='modal' data-id='{$row['id']}'><i class='fa fa-edit'></i></a></span>
                                    </td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['firstname']} {$row['lastname']}</td>
                                    
                                    <td>{$row['store']}</td>
                                    <td> 
                                        <img class='pic' src='{$images}'>
                                        <img class='picbig' src='{$images}'>
                                    </td>
                                    <td>{$row['tin_number']}</td>
                                    <td>
                                    <button class='btn btn-info btn-sm view btn-flat' style='color: #fff; border-radius: 8px;' 
                                    data-id='".$row['id']."'><i class='fa fa-eye'></i> View</button>
                                    </td>
                                    <td>
                                        <button class='btn btn-info btn-sm view-documents btn-flat' style='border-radius: 8px;' data-id='{$row['id']}'><i class='fa fa-eye'></i> View</button>
                                    </td>
                                    <td>
                                        {$status}
                                        {$active}
                                    </td>
                                    <td>" . date('M d, Y', strtotime($row['created_on'])) . "</td>
                                    <td>{$actionButtons}</td>
                                </tr>
                            ";
                        }
                    } catch (PDOException $e) {
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
    <?php include 'includes/vendor_modal.php'; ?>

</div>
<!-- ./wrapper -->

<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
    $(document).on('click', '.view-documents', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        $('#viewDocuments').modal('show');
        loadDocuments(id);
    });

    function loadDocuments(id) {
        $.ajax({
            type: 'POST',
            url: 'get_vendor_documents',
            data: {id: id},
            dataType: 'json',
            success: function(response) {
                if(!response.error) {
                    const documentTypes = ['business_permit', 'bir_doc', 'dti_doc', 'mayor_permit', 'valid_id'];
                    
                    documentTypes.forEach(docType => {
                        const docPath = response[docType];
                        updateDocumentDisplay(docType, docPath);
                    });
                } else {
                    showError("Error loading documents: " + response.error);
                }
            },
            error: function(xhr, status, error) {
                showError("Failed to load documents");
            }
        });
    }

    function updateDocumentDisplay(docType, docPath) {
        const previewDiv = $('#preview_' + docType);
        const downloadBtn = $('.download-doc[data-type="' + docType + '"]');
        const viewBtn = $('.view-doc[data-type="' + docType + '"]');
        
        if(docPath) {
            if(isImageFile(docPath)) {
                previewDiv.html(`<img src="../images/${docPath}" alt="Preview" style="max-width: 100px; max-height: 100px;">`);
            } else {
                previewDiv.html('<i class="fa fa-file-pdf-o fa-3x"></i>');
            }
            
            downloadBtn
                .attr('href', '../images/' + docPath)
                .attr('download', '')  
                .removeClass('disabled');
            viewBtn
                .data('path', '../images/' + docPath)
                .data('type', isImageFile(docPath) ? 'image' : 'pdf')
                .removeClass('disabled');
        } else {
            previewDiv.html('<span class="text-muted">No document</span>');
            downloadBtn.addClass('disabled');
            viewBtn.addClass('disabled');
        }
    }
    function isImageFile(filename) {
        return /\.(jpg|jpeg|png|gif)$/i.test(filename.toLowerCase());
    }

    $(document).on('click', '.view-doc', function(e) {
        e.preventDefault();
        if($(this).hasClass('disabled')) {
            showError("No document available for preview");
            return;
        }

        const docPath = $(this).data('path');
        const docType = $(this).data('type');
        
        if(docPath) {
            if(!$('#documentPreviewModal').length) {
                $('body').append(`
                    <div class="modal fade" id="documentPreviewModal">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Document Preview</h4>
                                </div>
                                <div class="modal-body text-center">
                                    <div id="documentViewer" style="min-height: 500px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }

            if(docType === 'image') {
                $('#documentViewer').html(`
                    <img src="${docPath}" style="max-width: 100%; max-height: 80vh; object-fit: contain;">
                `);
            } else {
                $('#documentViewer').html(`
                    <object data="${docPath}" type="application/pdf" width="100%" height="500px">
                        <p>Unable to display PDF. <a href="${docPath}" target="_blank">Click here to download</a></p>
                    </object>
                `);
            }
            
            $('#documentPreviewModal').modal('show');
        } else {
            showError("Document not found");
        }
    });

    $(document).on('click', '.download-doc', function(e) {
        if($(this).hasClass('disabled')) {
            e.preventDefault();
            showError("No document available for download");
        }
    });

    function showError(message) {
        swal({
            title: "Error",
            text: message,
            icon: "error",
            button: "OK"
        });
    }
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

  $(document).on('click', '.status', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

});
$(function() {
  $(document).on('click', '.accept', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    updateVendorStatus(id, 1);  // Accept = status 1
  });

  function updateVendorStatus(id, status) {
    $.ajax({
      type: 'POST',
      url: 'update_vendor_status',
      data: {id: id, status: status},
      success: function(response) {
        location.reload();  // Reload the page to reflect the changes
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });
  }
});
// Update your existing JavaScript code to handle the decline modal
$(function() {
  $(document).on('click', '.decline', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    $('#decline_vendor_id').val(id);
    $('#declineModal').modal('show');
  });

  $('#declineForm').on('submit', function(e) {
    e.preventDefault();
    var vendorId = $('#decline_vendor_id').val();
    var message = $('#decline_message').val();

    $.ajax({
      type: 'POST',
      url: 'decline_vendor',
      data: {
        id: vendorId,
        message: message,
        status: 5
      },
      success: function(response) {
        $('#declineModal').modal('hide');
        swal({
          title: "Vendor Declined",
          text: "Notification has been sent to the vendor",
          icon: "success",
          button: "OK"
        }).then(function() {
          location.reload();
        });
      },
      error: function(xhr, status, error) {
        console.error(error);
        swal({
          title: "Error",
          text: "There was an error processing your request",
          icon: "error",
          button: "OK"
        });
      }
    });
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
    url: 'vendor_row',
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

// Add click handler for the view button
$(function() {
    $(document).on('click', '.view', function(e) {
        e.preventDefault();
        $('#view').modal('show');
        var id = $(this).data('id');
        getRow(id);
    });
});

</script>
</body>
</html>
