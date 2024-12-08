<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
    // $conn = $pdo->open();

    // try {
    //     $user_id = isset($_SESSION['admin']) ? $_SESSION['admin'] : null;

    //     if ($user_id === null) {
    //         die("Error: User not logged in");
    //     }

    //     $stmt = $conn->prepare("SELECT type FROM users WHERE id = :user_id");
    //     $stmt->execute(['user_id' => $user_id]);
    //     $user = $stmt->fetch();

    //     if (!$user) {
    //         die("Error: User not found");
    //     }

    //     $user_type = $user['type'];
    //     if ($user_type != 1) {
    //         header('Location: home');
    //         exit();
    //     }
    // } catch (PDOException $e) {
    //     echo "There is some problem in connection: " . $e->getMessage();
    // }
?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Database Backup</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Backup</a></li>
        <li class="active">Export Database</li>
      </ol>
    </section>

    <br><br>

    <!-- Main content -->
    <section class="content">
      <div class="row">
      <div class="col-md-15 col-md-offset-3">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Database Management</h3>
            </div>
            <div class="box-body text-center">
              <p class="lead">Manage your database with the following options:</p>
              <a href="export_database" class="btn btn-lg btn-success">
                <i class="fa fa-database"></i> Export Database
              </a>
              <form action="import_database" method="post" enctype="multipart/form-data" style="margin-top: 15px;">
                <input type="file" name="import_file" required>
                <button type="submit" class="btn btn-lg btn-primary">
                  <i class="fa fa-upload"></i> Import Database
                </button>
              </form>
              <form action="drop_database" method="post" style="margin-top: 15px;">
                <button type="submit" class="btn btn-lg btn-danger" onclick="return confirm('Are you sure you want to drop the database? This action is irreversible!');">
                  <i class="fa fa-trash"></i> Drop Database
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->

  </div>
  <!-- /.content-wrapper -->

  <?php include 'includes/footer.php'; ?>
</div>
<!-- ./wrapper -->

<?php include 'includes/scripts.php'; ?>

</body>
</html>
