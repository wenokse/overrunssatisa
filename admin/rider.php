<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>
  <script src="../js/sweetalert.min.js"></script>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Rider</h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Rider</li>
      </ol>
    </section>

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
            <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat" style="border-radius: 8px;">
                <i class="fa fa-plus"></i> Add Rider
            </a>
          </div>
          <div class="box-body">
            <table id="example1" class="table table-bordered">
              <thead>
              <th class="hidden"></th>
              <th>Photo</th>
              <th>Phone Number</th>
              <th>Name</th>
              <th>Status</th>
              <th>Date Added</th>
              <th>Action</th>
              </thead>
              <tbody>
              <?php
              $conn = $pdo->open();
              try {
                $stmt = $conn->prepare("SELECT * FROM rider ORDER BY id DESC");
                $stmt->execute();
                foreach ($stmt as $row) {
                  $image = (!empty($row['photo'])) ? '../images/' . htmlspecialchars($row['photo'], ENT_QUOTES, 'UTF-8') : '../images/profile.jpg';
                  $status = ($row['status']) ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Deactive</span>';
                  $active = (!$row['status']) ? '<span class="pull-right"><a href="#activate" class="status" data-toggle="modal" data-id="' . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . '"><i class="fa fa-check-square-o"></i></a></span>' : '<span class="pull-right"><a href="#deactivate" class="status" data-toggle="modal" data-id="' . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . '"><i class="fa fa-check-square-o"></i></a></span>';
                  echo "
                    <tr>
                      <td class='hidden'></td>
                      <td>
                        <img class='pic' src='" . $image . "'>
                        <img class='picbig' src='" . $image . "'>
                        <span class='pull-right'><a href='#edit_photo' class='photo' data-toggle='modal' data-id='" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "'><i class='fa fa-edit'></i></a></span>
                      </td>
                      <td>" . htmlspecialchars($row['contact_info'], ENT_QUOTES, 'UTF-8') . "</td>
                      <td>" . htmlspecialchars($row['fullname'], ENT_QUOTES, 'UTF-8') . "</td>
                      <td>
                        " . $status . "
                        " . $active . "
                      </td>
                      <td>" . date('M d, Y', strtotime($row['created_on'])) . "</td>
                      <td>
                        <button class='btn btn-success btn-sm edit btn-flat' style='border-radius: 8px;' data-id='" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "'><i class='fa fa-edit'></i> Edit</button>
                        <button class='btn btn-danger btn-sm delete btn-flat' style='border-radius: 8px;' data-id='" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "'><i class='fa fa-trash'></i> Delete</button>
                      </td>
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
  <?php include 'includes/rider_modal.php'; ?>
</div>
<!-- ./wrapper -->

<?php include 'includes/scripts.php'; ?>
<script>
    $(function () {
        $(document).on('click', '.edit', function (e) {
            e.preventDefault();
            $('#edit').modal('show');
            var id = $(this).data('id');
            getRow(id);
        });

        $(document).on('click', '.delete', function (e) {
            e.preventDefault();
            $('#delete').modal('show');
            var id = $(this).data('id');
            getRow(id);
        });

        $(document).on('click', '.photo', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            getRow(id);
        });

        $(document).on('click', '.status', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            getRow(id);
        });

    });

    function getRow(id) {
        $.ajax({
            type: 'POST',
            url: 'rider_row.php',
            data: {id: id},
            dataType: 'json',
            success: function (response) {
                $('.userid').val(response.id);
                $('#edit_fullname').val(response.fullname);
                $('#edit_contact_info').val(response.contact_info);
                $('.fullname').html(response.fullname);
            }
        });
    }
</script>
</body>
</html>
