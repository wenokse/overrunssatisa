<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
    $conn = $pdo->open();

    try {
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
        if ($user_type != 1) {
            header('Location: home');
            exit();
        }
    } catch (PDOException $e) {
        echo "There is some problem in connection: " . $e->getMessage();
    }
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
        <div class="col-md-6 col-md-offset-3">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Export Your Database</h3>
            </div>
            <div class="box-body text-center">
              <p class="lead">Backup your database in just one click. Click the button below to export the database.</p>
              <!-- Centering the button and giving it a more modern look -->
              <a href="export_database" class="btn btn-lg btn-success">
                <i class="fa fa-database"></i> Export Database
              </a>
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
