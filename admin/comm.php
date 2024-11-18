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
                  <th>Comment</th>
                  <th>Product</th>
                  <th>User</th>
                  <th>Date</th>
                  <th>Likes</th>
                  <th>Dislikes</th>
                  <th>Action</th>
                </thead>
                <tbody>
                <?php
                          $conn = $pdo->open();

                          try {
                            $now = date('Y-m-d');
                            
                            if ($user_type == 195) {
                              $stmt = $conn->prepare("SELECT c.*, p.name AS product_name, u.firstname, u.lastname,
                                                      (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id AND action = 'like') AS likes,
                                                      (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id AND action = 'dislike') AS dislikes
                                                      FROM comment c 
                                                      LEFT JOIN products p ON p.id = c.product_id 
                                                      LEFT JOIN users u ON u.id = c.user_id 
                                                      ORDER BY c.created_at DESC");
                              $params = array();  
                          } else {
                              $stmt = $conn->prepare("SELECT c.*, p.name AS product_name, u.firstname, u.lastname,
                                                      (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id AND action = 'like') AS likes,
                                                      (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id AND action = 'dislike') AS dislikes
                                                      FROM comment c 
                                                      LEFT JOIN products p ON p.id = c.product_id 
                                                      LEFT JOIN users u ON u.id = c.user_id 
                                                      WHERE p.user_id = :user_id
                                                      ORDER BY c.created_at DESC");
                              $params = array(':user_id' => $user_id);  
                          }
                          
                          $stmt->execute($params);

                            foreach ($stmt as $row) {
                                echo "
                                  <tr>
                                    <td class='hidden'></td>
                                    <td>{$row['comment']}</td>
                                    <td>{$row['product_name']}</td>
                                    <td>{$row['firstname']} {$row['lastname']}</td>
                                    <td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>
                                    <td>{$row['likes']}</td>
                                    <td>{$row['dislikes']}</td>
                                    <td>
                                      "; 
                                      if ($user_type != 195) {  
                                        echo "
                                          <button type='button' class='btn btn-info btn-flat reply' style='color: #fff; border-radius: 8px;' data-id='{$row['id']}' data-toggle='modal' data-target='#replyModal'>
                                            <i class='fa fa-reply'></i> Reply
                                          </button>
                                          <button class='btn btn-danger delete-comment btn-flat' style='color: #fff; border-radius: 8px;' data-id='{$row['id']}'>
                                            <i class='fa fa-trash'></i> Delete
                                          </button>
                                        ";
                                      }
                                    echo "
                                    </td>
                                  </tr>
                                ";
                            }
                          }
                          catch (PDOException $e) {
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
  
  <!-- Reply Modal -->
  <div class="modal fade" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="replyModalLabel">Reply to Comment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="replyForm">
          <div class="modal-body">
            <input type="hidden" id="parent_id" name="parent_id">
            <div class="form-group">
              <label for="reply">Your Reply</label>
              <textarea class="form-control" id="reply" name="reply" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit Reply</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- ./wrapper -->

<?php include 'includes/scripts.php'; ?>
<script>

  
$(function(){
  // Open reply modal and set parent_id
  $(document).on('click', '.reply', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    $('#parent_id').val(id);
    $('#replyModal').modal('show');
  });

  // Handle reply form submission
  $('#replyForm').submit(function(e){
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
      type: 'POST',
      url: 'reply_comment',
      data: formData,
      dataType: 'json',
      success: function(response){
        if(response.success){
          $('#replyModal').modal('hide');
          swal({
            title: "Success!",
            text: response.message,
            icon: "success",
          }).then(() => {
            location.reload();
          });
        } else {
          swal({
            title: "Error!",
            text: response.message,
            icon: "error",
          });
        }
      },
      error: function(xhr, status, error) {
        console.error(xhr.responseText);
        swal({
          title: "Error!",
          text: "An error occurred while submitting the reply.",
          icon: "error",
        });
      }
    });
  });

  // Handle comment deletion
  $(document).on('click', '.delete-comment', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    swal({
      title: "Are you sure?",
      text: "Once deleted, you will not be able to recover this comment!",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) {
        $.ajax({
          type: 'POST',
          url: 'delete_comment',
          data: {id: id},
          dataType: 'json',
          success: function(response){
            if(response.success){
              swal("Comment deleted successfully!", {
                icon: "success",
              }).then(() => {
                location.reload();
              });
            } else {
              swal("Error deleting comment!", {
                icon: "error",
              });
            }
          },
          error: function(xhr, status, error) {
            console.error(xhr.responseText);
            swal("Error!", "An error occurred while deleting the comment.", "error");
          }
        });
      }
    });
  });
});
</script>
</body>
</html>